@extends('layouts.master')

@push('vendor-style')
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css" />
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.2.9/css/responsive.bootstrap.min.css" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
@endpush

@push('vendor-script')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.2.9/js/dataTables.responsive.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
@endpush

@push('page-script')
    <script>
        $(document).ready(function() {
            $('.select2').select2({
                width: '100%',
                placeholder: 'Pilih Jenis Kelamin',
                allowClear: true
            });

            var table = $('#userTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('userData.index') }}",
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex'
                    },
                    {
                        data: 'name',
                        name: 'name'
                    },
                    {
                        data: 'email',
                        name: 'email'
                    },
                    {
                        data: 'gender_label',
                        name: 'gender'
                    },
                    {
                        data: 'action',
                        orderable: false,
                        searchable: false
                    },
                ]
            });

            $('#userForm').on('submit', function(e) {
                e.preventDefault();
                $.ajax({
                    url: "{{ route('userData.store') }}",
                    type: "POST",
                    data: $(this).serialize(),
                    success: function(response) {
                        Swal.fire('Berhasil!', 'Data telah diproses.', 'success');
                        resetForm();
                        table.ajax.reload();
                    },
                    error: function(xhr) {
                        let errors = xhr.responseJSON.errors;
                        let errorMsg = '';
                        if (errors) {
                            $.each(errors, function(key, value) {
                                errorMsg += value[0] + '<br>';
                            });
                        } else {
                            errorMsg = 'Terjadi kesalahan sistem.';
                        }
                        Swal.fire('Error!', errorMsg, 'error');
                    }
                });
            });
        });

        function resetForm() {
            $('#userForm')[0].reset();
            $('#user_id').val('');
            $('#gender').val(null).trigger('change');
            $('.btn-submit').text('Submit');
        }

        function viewUser(id) {
            $.get("/userData-edit/" + id, function(data) {
                $('#view_name').text(data.name);
                $('#view_email').text(data.email);
                $('#view_gender').text(data.gender == 'L' ? 'Laki-laki' : 'Perempuan');
                $('#view_created').text(new Date(data.created_at).toLocaleString('id-ID'));
                $('#modalDetail').modal('show');
            });
        }

        function editUser(id) {
            $.get("/userData-edit/" + id, function(data) {
                $('#user_id').val(data.id);
                $('#name').val(data.name);
                $('#email').val(data.email);
                $('#gender').val(data.gender).trigger('change');
                $('#password').val('');
                $('.btn-submit').text('Update');
            });
        }

        function deleteUser(id) {
            Swal.fire({
                title: 'Hapus data?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                confirmButtonText: 'Ya'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: "/userData-delete/" + id,
                        type: "DELETE",
                        data: {
                            _token: "{{ csrf_token() }}"
                        },
                        success: function() {
                            $('#userTable').DataTable().ajax.reload();
                            Swal.fire('Terhapus!', 'Data telah dihapus.', 'success');
                        }
                    });
                }
            });
        }
    </script>
@endpush

@section('content')
    <div class="row">
        <div class="col-lg-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Form Pengguna</h5>
                </div>
                <div class="card-body">
                    <form id="userForm">
                        @csrf
                        <input type="hidden" name="user_id" id="user_id">
                        <div class="mb-3">
                            <label class="form-label">Nama Pengguna</label>
                            <input type="text" name="name" id="name" class="form-control"
                                placeholder="Masukkan nama lengkap" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Email</label>
                            <input type="email" name="email" id="email" class="form-control"
                                placeholder="email@example.com" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Password</label>
                            <input type="password" name="password" id="password" class="form-control"
                                placeholder="Min 6 karakter ">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Jenis Kelamin</label>
                            <select name="gender" id="gender" class="form-control select2" required>
                                <option value=""></option>
                                <option value="L">Laki-laki</option>
                                <option value="P">Perempuan</option>
                            </select>
                        </div>
                        <div class="text-end d-flex justify-content-end gap-2">
                            <button type="button" onclick="resetForm()" class="btn btn-light">Batal</button>
                            <button type="submit" class="btn btn-primary btn-submit">Submit</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header border-0 pb-0">
                    <h5 class="mb-0">Daftar Pengguna</h5>
                </div>
                <div class="card-body">
                    <table id="userTable"
                        class="table table-bordered dt-responsive nowrap table-striped align-middle w-100">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Nama</th>
                                <th>Email</th>
                                <th>Jenis Kelamin</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modalDetail" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Detail Pengguna</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row mb-2">
                        <div class="col-4 fw-bold">Nama</div>
                        <div class="col-8">: <span id="view_name"></span></div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-4 fw-bold">Email</div>
                        <div class="col-8">: <span id="view_email"></span></div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-4 fw-bold">Gender</div>
                        <div class="col-8">: <span id="view_gender"></span></div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-4 fw-bold">Terdaftar</div>
                        <div class="col-8">: <span id="view_created"></span></div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>
@endsection
