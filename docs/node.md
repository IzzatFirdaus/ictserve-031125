<!--
Guidance: Keep this file short and actionable. It documents how to switch Node.js versions
when using Laragon, how to verify the active version, and quick troubleshooting steps.
-->
# Node.js (Laragon) — switching, verification, and troubleshooting

This page explains how to switch Node.js versions in Laragon, verify the active version
in a PowerShell terminal, and provides a temporary workaround if terminals still show
the old version.

**When to use**: Vite and many modern frontend tools require Node >= 20.19 (or >= 22.12).
If `npm run dev` or `vite` errors with `crypto.hash is not a function` or a message
saying your Node version is too low, follow the steps below.

**1) Switch Node.js in Laragon (GUI)**
- Right-click the Laragon tray icon → `Node.js` → `Version` → select the version you want (for example `node-v22`).
- See the Laragon menu screenshot for the location of the `Version` menu.

**2) Why you must open a new terminal**
- Laragon updates its Node installation, but existing terminal processes (including VS Code integrated terminals)
  keep the old PATH and continue to use the previously active `node.exe`.
- After switching Node in Laragon, close any open terminals (and ideally restart VS Code) and open a new PowerShell terminal.

**3) Verify the active Node version (PowerShell)**
Run these commands in a new terminal:

```powershell
node --version           # shows active Node.js version
where.exe node           # shows the node executables found in PATH order
```

If `node --version` still shows an older version, `where.exe node` helps identify which `node.exe` is being picked up first.

**4) Temporary workaround (single terminal session)**
If you need to run the dev server immediately and cannot restart Laragon/VS Code right away, prepend the Laragon v22 folder to the PATH in the current PowerShell session:

```powershell
#$env:PATH = "C:\laragon\bin\nodejs\node-v22;$env:PATH"; npm run dev
$env:PATH = 'C:\laragon\bin\nodejs\node-v22;$env:PATH'
npm run dev
```

Or call the node binary directly when you want to run a single command:

```powershell
C:\laragon\bin\nodejs\node-v22\node.exe --version
C:\laragon\bin\nodejs\node-v22\node.exe node_modules\npm\bin\npm-cli.js run dev
```

**5) Troubleshooting checklist**
- If `where.exe node` shows more than one path, check the order. The first match is the `node.exe` that will run.
- If a `node` binary exists inside your project folder (for example `C:\laragon\www\your-project\node`), it may shadow the global one — remove or rename it if it is accidental.
- If Laragon's GUI switch doesn't appear to apply, fully stop Laragon and start it again, then open a fresh terminal.
- If VS Code's integrated terminal still shows the old version, restart VS Code after switching Laragon's Node version.

**6) Optional — document project Node requirement**
Add an `engines` field to `package.json` to make the required Node version explicit:

```json
"engines": {
  "node": ">=20.19.0"
}
```

This does not enforce the version by itself, but it documents the requirement for contributors and CI.

**7) Example: reproduce what we did in this repo**
- Confirm Laragon has `node-v22` installed: `ls C:\laragon\bin\nodejs`
- Confirm direct execution: `C:\laragon\bin\nodejs\node-v22\node.exe --version` (should print `v22.x.x`).
- Start dev server (temporary PATH):

```powershell
$env:PATH = 'C:\laragon\bin\nodejs\node-v22;$env:PATH'
npm run dev
```

If Vite reports ready (for example `VITE v7.2.0  ready in ...`), the switch worked.

---
Created: 2025-12-08 — short Laragon Node.js troubleshooting guide for the ICTServe project.
