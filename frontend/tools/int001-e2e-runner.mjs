import { spawn } from 'node:child_process';
import { createWriteStream, existsSync, mkdirSync, rmSync, writeFileSync } from 'node:fs';
import { tmpdir } from 'node:os';
import { dirname, join, resolve } from 'node:path';
import { fileURLToPath } from 'node:url';
import http from 'node:http';
import net from 'node:net';

const __dirname = dirname(fileURLToPath(import.meta.url));
const frontendRoot = resolve(__dirname, '..');
const repoRoot = resolve(frontendRoot, '..');
const backendRoot = join(repoRoot, 'backend');
const phpBin = process.env.HERD_PHP ?? 'C:\\Users\\hamza\\.config\\herd\\bin\\php85\\php.exe';
const nodeBin = process.execPath;
const npxCommand = process.platform === 'win32'
  ? { command: process.env.ComSpec ?? 'cmd.exe', args: ['/d', '/c', 'npx.cmd'] }
  : { command: 'npx', args: [] };
const playwrightConfig = process.env.INT001_PLAYWRIGHT_CONFIG ?? 'playwright.int001.config.ts';
const runId = `int001-${Date.now()}-${process.pid}`;
const workDir = join(tmpdir(), runId);
const logsDir = join(workDir, 'logs');
const databasePath = join(workDir, 'portfolio-int001.sqlite');
const ownedProcesses = [];

mkdirSync(logsDir, { recursive: true });
writeFileSync(databasePath, '');

const frontendPort = await freePort();
const apiPort = await freePort();
const frontendOrigin = `http://127.0.0.1:${frontendPort}`;
const apiOrigin = `http://127.0.0.1:${apiPort}`;
const apiBaseUrl = `${apiOrigin}/api/v1`;

const e2eEnv = {
  ...process.env,
  APP_ENV: 'e2e',
  APP_DEBUG: 'false',
  APP_URL: apiOrigin,
  CACHE_STORE: 'array',
  CORS_ALLOWED_ORIGINS: frontendOrigin,
  DB_CONNECTION: 'sqlite',
  DB_DATABASE: databasePath,
  MAIL_MAILER: 'log',
  PUBLIC_SITE_URL: frontendOrigin,
  PUBLIC_VISITOR_COOKIE: 'e2e_portfolio_visitor',
  QUEUE_CONNECTION: 'sync',
  SESSION_DRIVER: 'array',
  VISITOR_HASH_SECRET: 'int001-e2e-visitor-secret',
};

const frontendEnv = {
  ...process.env,
  INT001_API_BASE_URL: apiBaseUrl,
  INT001_API_ORIGIN: apiOrigin,
  NG_ALLOWED_HOSTS: '127.0.0.1,localhost',
  PLAYWRIGHT_BASE_URL: frontendOrigin,
  PORT: String(frontendPort),
  PORTFOLIO_API_BASE_URL: apiBaseUrl,
  PORTFOLIO_PUBLIC_ORIGIN: frontendOrigin,
};

try {
  console.log(`[int001] work dir: ${workDir}`);
  console.log(`[int001] frontend: ${frontendOrigin}`);
  console.log(`[int001] api: ${apiOrigin}`);

  await run(phpBin, ['artisan', 'migrate', '--force', '--no-interaction'], { cwd: backendRoot, env: e2eEnv, name: 'migrate' });
  await run(phpBin, ['artisan', 'db:seed', '--class=E2EInteractionSeeder', '--force', '--no-interaction'], { cwd: backendRoot, env: e2eEnv, name: 'seed' });

  const api = start(phpBin, ['artisan', 'serve', '--host=127.0.0.1', `--port=${apiPort}`, '--no-reload'], {
    cwd: backendRoot,
    env: e2eEnv,
    name: 'laravel',
  });
  const frontend = start(nodeBin, ['dist/portfolio-frontend/server/server.mjs'], {
    cwd: frontendRoot,
    env: frontendEnv,
    name: 'angular-ssr',
  });

  console.log(`[int001] started Laravel PID ${api.pid}`);
  console.log(`[int001] started Angular SSR PID ${frontend.pid}`);

  const apiReadiness = await waitFor(`${apiBaseUrl}/site?locale=en`, {
    headers: { Origin: frontendOrigin },
    name: 'Laravel API',
    validate: (response) => response.statusCode === 200,
  });
  console.log(`[int001] API CORS readiness headers: origin=${apiReadiness.headers['access-control-allow-origin'] ?? '<missing>'}, credentials=${apiReadiness.headers['access-control-allow-credentials'] ?? '<missing>'}`);
  await waitFor(`${frontendOrigin}/en`, {
    name: 'Angular SSR',
    validate: (response) => response.statusCode === 200,
  });

  await run(npxCommand.command, [...npxCommand.args, 'playwright', 'test', `--config=${playwrightConfig}`], {
    cwd: frontendRoot,
    env: frontendEnv,
    name: 'playwright-int001',
  });
} catch (error) {
  console.error(`[int001] failed: ${error instanceof Error ? error.message : String(error)}`);
  console.error(`[int001] logs are in ${logsDir}`);
  throw error;
} finally {
  await cleanup();
}

async function cleanup() {
  for (const child of ownedProcesses.reverse()) {
    if (child.exitCode === null && child.signalCode === null) {
      console.log(`[int001] stopping ${child.int001Name} PID ${child.pid}`);
      child.kill('SIGTERM');
      await waitForExit(child, 5000).catch(() => {
        console.log(`[int001] force stopping ${child.int001Name} PID ${child.pid}`);
        child.kill('SIGKILL');
      });
    }
  }

  rmSync(workDir, { recursive: true, force: true });
  console.log(`[int001] removed temp database and work dir: ${workDir}`);
}

function start(command, args, options) {
  const child = spawn(command, args, {
    cwd: options.cwd,
    env: options.env,
    shell: false,
    windowsHide: true,
  });
  child.int001Name = options.name;
  pipeLogs(child, options.name);
  ownedProcesses.push(child);
  return child;
}

function run(command, args, options) {
  return new Promise((resolveRun, rejectRun) => {
    const child = spawn(command, args, {
      cwd: options.cwd,
      env: options.env,
      shell: false,
      windowsHide: true,
    });
    child.int001Name = options.name;
    pipeLogs(child, options.name);
    child.on('error', rejectRun);
    child.on('exit', (code) => {
      if (code === 0) {
        resolveRun();
      } else {
        rejectRun(new Error(`${options.name} exited with ${code}`));
      }
    });
  });
}

function pipeLogs(child, name) {
  const stream = createWriteStream(join(logsDir, `${name}.log`), { flags: 'a' });
  child.stdout?.pipe(stream, { end: false });
  child.stderr?.pipe(stream, { end: false });
  child.stdout?.on('data', (data) => process.stdout.write(`[${name}] ${data}`));
  child.stderr?.on('data', (data) => process.stderr.write(`[${name}] ${data}`));
}

function waitForExit(child, timeoutMs) {
  return new Promise((resolveExit, rejectExit) => {
    const timeout = setTimeout(() => rejectExit(new Error('Timed out waiting for process exit')), timeoutMs);
    child.once('exit', () => {
      clearTimeout(timeout);
      resolveExit();
    });
  });
}

function waitFor(url, options) {
  const deadline = Date.now() + 30_000;

  return new Promise((resolveWait, rejectWait) => {
    const attempt = () => {
      request(url, options.headers ?? {})
        .then((response) => {
          if (options.validate(response)) {
            resolveWait(response);
            return;
          }

          retry(`unexpected status/header for ${options.name}: status=${response.statusCode}, headers=${JSON.stringify(response.headers)}`);
        })
        .catch((error) => retry(error.message));
    };

    const retry = (reason) => {
      if (Date.now() > deadline) {
        rejectWait(new Error(`${options.name} readiness failed: ${reason}`));
        return;
      }

      setTimeout(attempt, 250);
    };

    attempt();
  });
}

function request(url, headers) {
  return new Promise((resolveRequest, rejectRequest) => {
    const req = http.get(url, { headers }, (response) => {
      response.resume();
      response.on('end', () => resolveRequest(response));
    });
    req.on('error', rejectRequest);
    req.setTimeout(2000, () => {
      req.destroy(new Error(`Timed out requesting ${url}`));
    });
  });
}

function freePort() {
  return new Promise((resolvePort, rejectPort) => {
    const server = net.createServer();
    server.unref();
    server.on('error', rejectPort);
    server.listen(0, '127.0.0.1', () => {
      const address = server.address();
      const port = typeof address === 'object' && address ? address.port : 0;
      server.close(() => resolvePort(port));
    });
  });
}
