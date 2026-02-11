/**
 * Simple scheduler runner for testbench.
 * Runs `php vendor/bin/testbench schedule:run` every 60 seconds.
 *
 */
const { execSync } = require("child_process");

const INTERVAL = 60_000; // 1 minute

function runSchedule() {
    try {
        execSync("php vendor/bin/testbench schedule:run --no-interaction 2>&1", {
            stdio: "inherit",
            cwd: __dirname,
        });
    } catch (e) {
        console.error("[scheduler] Error running schedule:", e);}
}

console.log("[scheduler] Starting scheduler (every 60s)...");

runSchedule();
setInterval(runSchedule, INTERVAL);
