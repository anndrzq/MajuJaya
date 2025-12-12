@extends('layouts.master')

@push('vendor-style')
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css" />
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.2.9/css/responsive.bootstrap.min.css" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
@endpush

@push('vendor-script')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.2.9/js/dataTables.responsive.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
@endpush

@push('page-script')
    <script>
        $(document).ready(function() {
            $('#example').DataTable({
                responsive: true,
            });

            @if (session('success'))
                Swal.fire({
                    title: "BERHASIL!",
                    text: "{{ session('success') }}",
                    icon: "success"
                });
            @endif
        });
    </script>
@endpush

@section('title', 'Histori Penjualan')

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0">Histori Penjualan</h4>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="javascript: void(0);">Laporan</a></li>
                        <li class="breadcrumb-item active">Kasir</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <div class="row mt-3">
        <div class="col-12">
            <div class="card">

                <div class="card-body">
                    <table id="example"
                        class="table table-bordered table-striped align-middle dt-responsive nowrap w-100">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Nomor Invoice</th>
                                <th>Tanggal Transaksi</th>
                                <th>Total Harga</th>
                                <th>Bayar</th>
                                <th>Kembalian</th>
                                <th>Kasir</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($transactions as $trx)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $trx->invoice_number }}</td>
                                    <td>{{ $trx->created_at->translatedFormat('d F Y H:i') }}</td>
                                    <td>Rp {{ number_format($trx->total_price, 0, ',', '.') }}</td>
                                    <td>Rp {{ number_format($trx->pay_amount, 0, ',', '.') }}</td>
                                    <td>Rp {{ number_format($trx->change_amount, 0, ',', '.') }}</td>
                                    <td>{{ $trx->user->name ?? '-' }}</td>
                                    <td>
                                        <button class="btn btn-info btn-sm" data-bs-toggle="modal"
                                            data-bs-target="#modalView-{{ $trx->id }}">
                                            <i class="ri-eye-line"></i> Detail
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>

                    {{-- Modal Detail --}}
                    @foreach ($transactions as $trx)
                        <div id="modalView-{{ $trx->id }}" class="modal fade" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog modal-lg">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title">Detail Transaksi - {{ $trx->invoice_number }}</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                                            aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <div class="row mb-3">
                                            <div class="col-md-6">
                                                <p class="mb-1 text-muted">Tanggal</p>
                                                <h6 class="fw-semibold">{{ $trx->created_at->format('d/m/Y H:i') }}</h6>
                                            </div>
                                            <div class="col-md-6 text-md-end">
                                                <p class="mb-1 text-muted">Kasir</p>
                                                <h6 class="fw-semibold">{{ $trx->user->name ?? '-' }}</h6>
                                            </div>
                                        </div>

                                        <table class="table table-sm table-bordered">
                                            <thead class="bg-light">
                                                <tr>
                                                    <th>Produk</th>
                                                    <th class="text-center">Qty</th>
                                                    <th class="text-end">Harga Satuan</th>
                                                    <th class="text-end">Subtotal</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($trx->details as $detail)
                                                    <tr>
                                                        <td>{{ $detail->product->name ?? 'Produk Dihapus' }}</td>
                                                        <td class="text-center">{{ $detail->quantity }}</td>
                                                        <td class="text-end">Rp
                                                            {{ number_format($detail->selling_price, 0, ',', '.') }}</td>
                                                        <td class="text-end">Rp
                                                            {{ number_format($detail->subtotal, 0, ',', '.') }}</td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                            <tfoot>
                                                <tr>
                                                    <th colspan="3" class="text-end">Grand Total</th>
                                                    <th class="text-end">Rp
                                                        {{ number_format($trx->total_price, 0, ',', '.') }}</th>
                                                </tr>
                                            </tfoot>
                                        </table>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary"
                                            data-bs-dismiss="modal">Tutup</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
@endsection
