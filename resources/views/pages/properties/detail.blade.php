@extends('app')
@section('content')
    <div class="col-12">
        <div class="card">
            <div class="card-content">
                <div class="card-header">
                    <div class="row">
                        <div class="col-12 col-lg-12">
                            <h3 class="card-title">
                                Detail Kontrakan {{ $property->name }}
                            </h3>
                            <nav aria-label="breadcrumb">
                                <ol class="breadcrumb breadcrumb-style1">
                                    <li class="breadcrumb-item">
                                        <a href="{{ route('properties.index') }}"
                                            class="text-decoration-underline">Kontrakan</a>
                                    </li>
                                    <li class="breadcrumb-item active">Detail</li>
                                </ol>
                            </nav>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    {{-- properties --}}
                    <div class="row row-cols-1 row-cols-md-3 g-6 my-5">
                        <div class="col-12 col-lg-6">
                            @if ($property->image)
                                <img class="card-img-top" src="{{ asset('storage/' . $property->image) }}"
                                    alt="Card image cap" />
                            @else
                                <img class="card-img-top" src="{{ asset('assets/img/image_not_available.png') }}"
                                    alt="Card image cap" />
                            @endif
                        </div>
                        <div class="col-12 col-lg-6 ">
                            <h1 class="text-secondary-emphasis">{{ $property->name }}</h1>
                            <h5 class="text-secondary">{{ $property->description }}</h5>
                                @php
                                    $status = false;
                                @endphp
                            @foreach ($property->leases as $lease)
                                @if ($lease->user->hasRole('admin'))
                                    <p style="color: blue;">Ketua Kontrakan: {{ $lease->user->name }}</p>
                                    @php
                                        $status = true
                                    @endphp
                                @endif
                            @endforeach
                            <p style="color: red;">
                                 {{ $status == false ? 'Belum Ada Ketua Kontrakan' : '' }}
                            </p>


                            <h2 class="fw-bold text-secondary my-6">
                                {{ 'Rp. ' . number_format($property->rental_price, 0) }}</h2>
                            <div class="badge fs-6 bg-label-secondary mt-6 me-3">Total Orang:
                                <strong>{{ $property->leases->count() }}</strong>
                            </div>
                            /
                            <div class="badge fs-6 bg-label-warning mt-6 me-3">Kapasitas:
                                <strong>{{ $property->capacity }}</strong>
                            </div>
                            @if ($property->status === 'available')
                                <div class="badge fs-6 bg-label-success mt-6">Tersedia</div>
                            @else
                                <div class="badge fs-6 bg-label-danger mt-6">Full</div>
                            @endif
                        </div>
                        
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- MAPS --}}
    <div class="col-12">
        <div class="card">
            <div class="card-content">
                <div class="card-header">
                    <div class="row">
                        <div class="col-12 col-lg-12">
                            <h3 class="card-title">
                                Location
                            </h3>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    {{-- content maps --}}

                    <div class="w-full md:w-1/3 bg-white shadow-lg rounded-lg overflow-hidden">
                        <div class="map-container">
                            <div style="width: 100%;height: 100vh" id="map"></div>
                        </div>
                    </div>


                </div>
            </div>
            {{-- {{ $properties->links() }} --}}
        </div>
    </div>

    <script>
        var lat = -7.896591;
        var lng = 112.6089657;
        var zoomLevel = 16;

        var map = L.map('map').setView([lat, lng], zoomLevel);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© OpenStreetMap contributors'
        }).addTo(map);

        var waypoints = [{
                latLng: L.latLng(<?php echo json_encode($property->langtitude); ?>, <?php echo json_encode($property->longtitude); ?>),
                title: <?php echo json_encode($property->name); ?>,
                address: <?php echo json_encode($property->address); ?>,
            },
            {
                latLng: L.latLng(-7.900063, 112.6068816),
                title: "Hummasoft / Hummatech (PT Humma Teknologi Indonesia)",
                address: "Perum Permata Regency 1, Blk. 10 No.28, Perun Gpa, Ngijo, Kec. Karang Ploso, Kabupaten Malang, Jawa Timur 65152"
            }
        ];

        var routingControl = L.Routing.control({
            waypoints: waypoints.map(function(wp) {
                return wp.latLng;
            }),
            routeWhileDragging: true,
            createMarker: function(i, wp, nWps) {
                var popupContent = waypoints[i].title + "<br><br><b>Address:</b>" + waypoints[i].address;
                var marker = L.marker(wp.latLng).bindPopup(popupContent);
                return marker;
            }
        }).addTo(map);
    </script>
@endsection
