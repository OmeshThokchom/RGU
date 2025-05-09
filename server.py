import subprocess
import os
from dotenv import load_dotenv
import signal
import sys

load_dotenv()  # Load .env variables

def shutdown():
    """Kill PHP and Ngrok processes"""
    print("\n🛑 Shutting down...")
    php_server.terminate()
    ngrok_process.terminate()
    sys.exit(0)

def handle_signal(signum, frame):
    shutdown()

# Start PHP server silently
php_server = subprocess.Popen(
    ["php", "-S", f"{os.getenv('PHP_HOST')}:{os.getenv('PHP_PORT')}"],
    stdout=subprocess.DEVNULL,
    stderr=subprocess.DEVNULL
)

# Start Ngrok in the foreground (shows CLI output)
ngrok_process = subprocess.Popen([
    "ngrok", "http",
    "--domain", os.getenv("NGROK_HOSTNAME"),
    os.getenv("PHP_PORT")
])

# Handle Ctrl+C gracefully
signal.signal(signal.SIGINT, handle_signal)

# Wait for Ngrok to exit (or be killed)
ngrok_process.wait()
shutdown()