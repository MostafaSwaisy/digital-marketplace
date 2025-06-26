@echo off
echo Stopping Digital Marketplace Microservices...

echo.
echo Attempting to stop services running on ports 8000-8003...

echo Stopping API Gateway (Port 8000)...
for /f "tokens=5" %%a in ('netstat -aon ^| findstr ":8000"') do taskkill /PID %%a /F 2>nul

echo Stopping User Service (Port 8001)...
for /f "tokens=5" %%a in ('netstat -aon ^| findstr ":8001"') do taskkill /PID %%a /F 2>nul

echo Stopping Product Service (Port 8002)...
for /f "tokens=5" %%a in ('netstat -aon ^| findstr ":8002"') do taskkill /PID %%a /F 2>nul

echo Stopping Order Service (Port 8003)...
for /f "tokens=5" %%a in ('netstat -aon ^| findstr ":8003"') do taskkill /PID %%a /F 2>nul

echo.
echo All services stopped!
echo.
pause