// PreToolUse (Edit|Write|MultiEdit): bloquea archivos que no se tocan a mano.
import { spawnSync } from 'node:child_process';
import { existsSync } from 'node:fs';
import path from 'node:path';
import { block, projectRoot, readEvent, relativeToRoot } from './lib.mjs';

const event = await readEvent();
const file = event.tool_input?.file_path;
if (!file) process.exit(0);

const root = projectRoot(event);
const rel = relativeToRoot(root, file);
const name = path.basename(file);

// .env y .env.* (menos .env.example): ni leerlo ni editarlo.
if (name === '.env' || (name.startsWith('.env.') && name !== '.env.example')) {
  block(`Bloqueado: ${name} tiene credenciales reales y Claude no lo lee ni lo edita. Para ver variables usá .env.example; para variables nuevas agregalas ahí y pedile al usuario que actualice su .env.`);
}

// Leer lockfiles y migraciones está bien; las reglas de abajo son solo para escribir.
if (event.tool_name === 'Read') process.exit(0);

// Lockfiles: los genera composer / npm.
if (name === 'composer.lock' || name === 'package-lock.json') {
  block(`Bloqueado: ${name} no se edita a mano. Usá composer/npm (por ejemplo composer update <paquete> o npm install).`);
}

// Migraciones existentes (versionadas en git): crear nuevas sí, modificar no.
if (rel.startsWith('database/migrations/') && rel.endsWith('.php')) {
  const git = spawnSync('git', ['ls-files', '--', rel], { cwd: root, encoding: 'utf8' });
  const tracked = git.status === 0 ? git.stdout.trim() !== '' : existsSync(path.resolve(root, rel));
  if (tracked) {
    block(`Bloqueado: ${rel} ya existe en git y puede estar aplicada en producción (hay datos reales). Creá una migración nueva en lugar de modificarla (ver skill nueva-migracion-segura).`);
  }
}

process.exit(0);
