@extends('layouts.app')

@section('title', 'Dashboard - Digital Marketplace')

@section('content')
<div class="row">
    <div class="col-12">
        <h1 class="mb-4">Microservices Dashboard</h1>
    </div>
</div>

<!-- Simple Service Status -->
<div class="row mb-4">
    <div class="col-12">
        <h3>Service Status</h3>
    </div>
    
    <div class="col-md-3">
        <div class="card">
            <div class="card-body text-center">
                <h5>API Gateway</h5>
                <p>Port: 8000</p>
                <span class="badge bg-success">You're here!</span>
            </div>
        </div>
    </div>
    
    <div class="col-md-3">
        <div class="card">
            <div class="card-body text-center">
                <h5>User Service</h5>
                <p>Port: 8001</p>
                <a href="http://localhost:8001/api/test" target="_blank" class="btn btn-sm btn-primary">Test</a>
            </div>
        </div>
    </div>
    
    <div class="col-md-3">
        <div class="card">
            <div class="card-body text-center">
                <h5>Product Service</h5>
                <p>Port: 8002</p>
                <a href="http://localhost:8002/api/test" target="_blank" class="btn btn-sm btn-primary">Test</a>
            </div>
        </div>
    </div>
    
    <div class="col-md-3">
        <div class="card">
            <div class="card-body text-center">
                <h5>Order Service</h5>
                <p>Port: 8003</p>
                <a href="http://localhost:8003/api/test" target="_blank" class="btn btn-sm btn-primary">Test</a>
            </div>
        </div>
    </div>
</div>

<!-- Simple Message -->
<div class="row">
    <div class="col-12">
        <div class="alert alert-info">
            <h4>Welcome to the Digital Marketplace Dashboard!</h4>
            <p>This is a microservices architecture demo. Click the "Test" buttons above to verify each service is running.</p>
            <p>All services should be running on your local machine.</p>
        </div>
    </div>
</div>

<!-- Basic Navigation -->
<div class="row">
    <div class="col-md-4">
        <div class="card">
            <div class="card-body">
                <h5>Users</h5>
                <p>Manage users and authentication</p>
                <a href="/users" class="btn btn-primary">Go to Users</a>
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <div class="card">
            <div class="card-body">
                <h5>Products</h5>
                <p>Manage digital products</p>
                <a href="/products" class="btn btn-success">Go to Products</a>
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <div class="card">
            <div class="card-body">
                <h5>Orders</h5>
                <p>Manage orders and transactions</p>
                <a href="/orders" class="btn btn-warning">Go to Orders</a>
            </div>
        </div>
    </div>
</div>
@endsection