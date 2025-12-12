@extends('layouts.master')

@section('content')
    <div class="row">
        <div class="col-xl-12">
            <div class="h-100">
                <div class="row mb-3 pb-1">
                    <div class="col-12">
                        <div class="d-flex align-items-lg-center flex-lg-row flex-column">
                            <div class="flex-grow-1">
                                <h4 class="fs-16 mb-1">Selamat Datang!</h4>
                                <p class="text-muted mb-0">Ringkasan operasional Toko Maju Jaya.</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-xl-4 col-md-6">
                        <div class="card card-animate">
                            <div class="card-body">
                                <p class="text-uppercase fw-medium text-muted mb-0">Total Pendapatan</p>
                                <div class="d-flex align-items-end justify-content-between mt-4">
                                    <div>
                                        <h4 class="fs-22 fw-semibold ff-secondary mb-4">Rp
                                            {{ number_format($totalEarnings, 0, ',', '.') }}</h4>
                                        <a href="/transaction-history" class="text-decoration-underline text-muted">Lihat
                                            Detail</a>
                                    </div>
                                    <div class="avatar-sm flex-shrink-0">
                                        <span class="avatar-title bg-success-subtle rounded fs-3">
                                            <i class="bx bx-dollar-circle text-success"></i>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-xl-4 col-md-6">
                        <div class="card card-animate">
                            <div class="card-body">
                                <p class="text-uppercase fw-medium text-muted mb-0">Jumlah Transaksi</p>
                                <div class="d-flex align-items-end justify-content-between mt-4">
                                    <div>
                                        <h4 class="fs-22 fw-semibold ff-secondary mb-4">{{ $totalOrders }}</h4>
                                        <p class="text-muted mb-0">Total Penjualan</p>
                                    </div>
                                    <div class="avatar-sm flex-shrink-0">
                                        <span class="avatar-title bg-info-subtle rounded fs-3">
                                            <i class="bx bx-shopping-bag text-info"></i>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-xl-4 col-md-6">
                        <div class="card card-animate">
                            <div class="card-body">
                                <p class="text-uppercase fw-medium text-muted mb-0">Varian Produk</p>
                                <div class="d-flex align-items-end justify-content-between mt-4">
                                    <div>
                                        <h4 class="fs-22 fw-semibold ff-secondary mb-4">{{ $totalProducts }}</h4>
                                        <a href="/products" class="text-decoration-underline text-muted">Cek Produk</a>
                                    </div>
                                    <div class="avatar-sm flex-shrink-0">
                                        <span class="avatar-title bg-warning-subtle rounded fs-3">
                                            <i class="bx bx-box text-warning"></i>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
@endsection
