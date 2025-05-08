import subprocess
from pyngrok import ngrok

# Start PHP built-in server (adjust path if needed)
php_server = subprocess.Popen(["php", "-S", "localhost:8000"])

# Create a tunnel
public_url = ngrok.connect(8000, bind_tls=True)
print(f"🔥 Server is LIVE at: {public_url}")

try:
    # Keep the tunnel running until you stop it
    input("Press Ctrl+C to quit.\n")
finally:
    print("🛑 Shutting down...")
    php_server.terminate()
    ngrok.kill()
