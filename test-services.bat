@echo off
echo Starting Digital Marketplace Microservices...

echo Starting User Service (Port 8001)...
start "User Service" cmd /k "cd user-service && cd user-service && php artisan serve --host=127.0.0.1 --port=8001"

echo Waiting 3 seconds...
timeout /t 3 /nobreak >nul

echo Starting Product Service (Port 8002)...
start "Product Service" cmd /k "cd product-service &&cd product-service && php artisan serve --host=127.0.0.1 --port=8002"

echo Waiting 3 seconds...
timeout /t 3 /nobreak >nul

echo Starting Order Service (Port 8003)...
start "Order Service" cmd /k "cd order-service && cd order-service && php artisan serve --host=127.0.0.1 --port=8003"

echo Waiting 3 seconds...
timeout /t 3 /nobreak >nul

echo Starting API Gateway (Port 8000)...
start "API Gateway" cmd /k "cd api-gateway && cd api-gateway && php artisan serve --host=127.0.0.1 --port=8000"

echo.
echo All services are starting!
echo User Service: http://localhost:8001
echo Product Service: http://localhost:8002
echo Order Service: http://localhost:8003
echo API Gateway: http://localhost:8000
echo Frontend: http://localhost:8000
echo Frontend Demo: http://localhost:8000/demo.html
echo.
echo Each service will open in a separate command window.
echo Close those windows to stop the services.
pause