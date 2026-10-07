@extends('layouts.admin')

@section('title', 'Seat map - ' . $event->name)

@section('content')
    @php
        $initialLayout = $event->seatMap?->layout ?? [
            'canvas' => ['width' => 1400, 'height' => 850],
            'objects' => [],
            'rows' => [],
        ];
        $ticketPrices = $ticketTypes->pluck('price', 'id');
        foreach ($initialLayout['objects'] ?? [] as $objectIndex => $object) {
            if (($object['type'] ?? null) === 'zone' && !array_key_exists('price', $object)) {
                $initialLayout['objects'][$objectIndex]['price'] = (float) ($ticketPrices[$object['ticket_type_id'] ?? 0] ?? 0);
            }
        }
    @endphp

    <div class="mx-auto max-w-[1600px] px-4 py-6 sm:px-6 lg:px-8">
        <div class="mb-5 flex flex-wrap items-center justify-between gap-3">
            <div>
                <p class="text-sm text-gray-500">{{ $event->name }}</p>
                <h1 class="text-2xl font-bold text-gray-900">Seat Map Builder</h1>
            </div>
            <a href="{{ route('admin.events.show', $event) }}"
                class="rounded-lg border bg-white px-4 py-2 font-semibold text-gray-700 hover:bg-gray-50">Quay lại Event</a>
        </div>

        <div class="grid min-h-[720px] gap-4 lg:grid-cols-[280px_minmax(0,1fr)]">
            <aside class="rounded-xl border bg-white p-4 shadow-sm">
                <h2 class="text-lg font-bold">{{ $event->sale_mode === 'assigned_seat' ? 'Sơ đồ ghế' : 'Sơ đồ khu vực' }}
                </h2>
                <p class="mt-1 text-sm text-gray-500">
                    {{ $event->sale_mode === 'assigned_seat' ? 'Mỗi dãy sẽ tạo ticket type và các ghế tương ứng.' : 'Mỗi khu sẽ tạo một ticket type theo số lượng và giá vé.' }}
                </p>

                <div class="mt-4 grid grid-cols-2 gap-2">
                    <button type="button" data-add="stage"
                        class="rounded-lg bg-gray-900 px-3 py-2 text-sm font-semibold text-white">+ Sân khấu</button>
                    <button type="button" data-add="entrance"
                        class="rounded-lg bg-green-600 px-3 py-2 text-sm font-semibold text-white">+ Lối vào</button>
                    <button type="button" data-add="exit"
                        class="rounded-lg bg-red-600 px-3 py-2 text-sm font-semibold text-white">+ Lối ra</button>
                    <button type="button" data-add="text"
                        class="rounded-lg bg-gray-200 px-3 py-2 text-sm font-semibold text-gray-700">+ Text</button>
                </div>

                <div class="mt-5 border-t pt-4">
                    <h3 class="font-semibold">{{ $event->sale_mode === 'assigned_seat' ? 'Thêm dãy ghế' : 'Thêm khu vực' }}
                    </h3>
                    <label
                        class="mt-3 block text-xs font-semibold text-gray-600">{{ $event->sale_mode === 'assigned_seat' ? 'Tên dãy / khu' : 'Tên khu' }}</label>
                    <input id="new-zone-name" value="A" class="mt-1 w-full rounded-lg border px-3 py-2 text-sm">
                    <label
                        class="mt-3 block text-xs font-semibold text-gray-600">{{ $event->sale_mode === 'assigned_seat' ? 'Số ghế' : 'Số vé' }}</label>
                    <input id="new-zone-count" type="number" min="1" value="10"
                        class="mt-1 w-full rounded-lg border px-3 py-2 text-sm">
                    <label class="mt-3 block text-xs font-semibold text-gray-600">Giá vé</label>
                    <input id="new-zone-price" type="number" min="0" step="0.01" value="0"
                        class="mt-1 w-full rounded-lg border px-3 py-2 text-sm">
                    <button type="button" id="add-zone"
                        class="mt-3 w-full rounded-lg bg-primary-600 px-3 py-2 text-sm font-semibold text-white hover:bg-primary-700">+
                        {{ $event->sale_mode === 'assigned_seat' ? 'Tạo dãy ghế' : 'Tạo khu vực' }}</button>
                </div>

                <div id="properties" class="mt-5 hidden border-t pt-4">
                    <h3 class="font-semibold">Đối tượng đang chọn</h3>
                    <label class="mt-3 block text-xs font-semibold text-gray-600">Tên</label>
                    <input id="edit-name" class="mt-1 w-full rounded-lg border px-3 py-2 text-sm">
                    <div id="zone-properties">
                        <label class="mt-3 block text-xs font-semibold text-gray-600">Số ghế</label>
                        <input id="edit-count" type="number" min="1"
                            class="mt-1 w-full rounded-lg border px-3 py-2 text-sm">
                        <label class="mt-3 block text-xs font-semibold text-gray-600">Giá vé</label>
                        <input id="edit-price" type="number" min="0" step="0.01"
                            class="mt-1 w-full rounded-lg border px-3 py-2 text-sm">
                    </div>
                    <button type="button" id="delete-object"
                        class="mt-4 w-full rounded-lg bg-red-600 px-3 py-2 text-sm font-semibold text-white hover:bg-red-700">Xóa
                        đối tượng</button>
                </div>

                <form id="save-map-form" action="{{ route('admin.events.seat-map.update', $event) }}" method="POST"
                    class="mt-5 border-t pt-4">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="layout" id="layout-input">
                    <button
                        class="w-full rounded-lg bg-primary-600 px-4 py-3 font-semibold text-white hover:bg-primary-700">Lưu
                        seat map</button>
                </form>

                <p class="mt-4 rounded-lg bg-gray-50 p-3 text-xs leading-5 text-gray-500">- Kéo đối tượng để di chuyển.
                    </br>
                    - Kéo chấm xanh để đổi kích thước.</br>- Kéo chấm xanh lá để xoay.</p>
            </aside>

            <section class="min-w-0 overflow-auto rounded-xl border bg-gray-100 p-3 shadow-sm">
                <div id="seat-map-canvas" class="relative origin-top-left rounded-lg border-2 border-gray-300 bg-white"
                    style="width: {{ $initialLayout['canvas']['width'] ?? 1400 }}px; height: {{ $initialLayout['canvas']['height'] ?? 850 }}px; background-image: linear-gradient(#e5e7eb 1px, transparent 1px), linear-gradient(90deg, #e5e7eb 1px, transparent 1px); background-size: 25px 25px;">
                </div>
            </section>
        </div>
    </div>

    <script>
        (() => {
            const canvas = document.getElementById('seat-map-canvas');
            const properties = document.getElementById('properties');
            const zoneProperties = document.getElementById('zone-properties');
            const initialLayout = @json($initialLayout);
            const isAssignedSeat = @json($event->sale_mode === 'assigned_seat');
            let objects = Array.isArray(initialLayout.objects) ? initialLayout.objects : [];
            let selectedId = null;
            let nextId = objects.reduce((max, object) => Math.max(max, Number(object.id) || 0), 0) + 1;
            let interaction = null;

            if (!objects.length && Array.isArray(initialLayout.rows)) {
                objects = initialLayout.rows.map((row, index) => ({
                    id: index + 1,
                    type: 'zone',
                    name: row.name || `Zone ${index + 1}`,
                    count: Number(row.seats || 1),
                    price: Number(row.price || 0),
                    zone: row.zone || row.name || '',
                    x: 100 + (index % 3) * 300,
                    y: 160 + Math.floor(index / 3) * 220,
                    width: 220,
                    height: 150,
                    rotation: 0,
                }));
                nextId = objects.length + 1;
            }

            const selected = () => objects.find(object => object.id === selectedId);

            function createObject(type, values = {}) {
                const defaults = {
                    id: nextId++, type, name: type === 'stage' ? 'SÂN KHẤU' : type === 'entrance' ? 'LỐI VÀO' : type === 'exit' ? 'LỐI RA' : 'GHI CHÚ',
                    x: 120, y: 100, width: type === 'stage' ? 360 : 180, height: type === 'stage' ? 70 : 55, rotation: 0,
                };
                objects.push({ ...defaults, ...values });
                selectedId = defaults.id;
                render();
            }

            function render() {
                canvas.innerHTML = '';
                objects.forEach(object => {
                    const element = document.createElement('div');
                    element.className = `seat-map-object seat-map-${object.type} ${object.id === selectedId ? 'seat-map-selected' : ''}`;
                    element.dataset.id = object.id;
                    element.style.left = `${object.x}px`;
                    element.style.top = `${object.y}px`;
                    element.style.width = `${object.width}px`;
                    element.style.height = `${object.height}px`;
                    element.style.transform = `rotate(${object.rotation || 0}deg)`;
                    element.innerHTML = `<strong>${escapeHtml(object.name || '')}</strong>`;

                    if (object.type === 'zone') {
                        const seats = document.createElement('div');
                        seats.className = isAssignedSeat ? 'seat-map-seats' : 'seat-map-capacity';
                        if (isAssignedSeat) {
                            for (let number = 1; number <= Number(object.count || 0); number++) {
                                const seat = document.createElement('span');
                                seat.textContent = String(object.name || '') + number;
                                seats.appendChild(seat);
                            }
                        } else {
                            seats.textContent = String(Number(object.count || 0)) + ' vé';
                        }
                        element.appendChild(seats);
                    }

                    if (object.id === selectedId) {
                        const resize = document.createElement('span');
                        resize.className = 'seat-map-resize';
                        resize.dataset.action = 'resize';
                        const rotate = document.createElement('span');
                        rotate.className = 'seat-map-rotate';
                        rotate.dataset.action = 'rotate';
                        element.append(resize, rotate);
                    }

                    canvas.appendChild(element);
                });
                updateProperties();
            }

            function updateProperties() {
                const object = selected();
                properties.classList.toggle('hidden', !object);
                if (!object) return;
                document.getElementById('edit-name').value = object.name || '';
                zoneProperties.classList.toggle('hidden', object.type !== 'zone');
                if (object.type === 'zone') {
                    document.getElementById('edit-count').value = object.count || 1;
                    document.getElementById('edit-price').value = object.price || 0;
                }
            }

            function escapeHtml(value) {
                const element = document.createElement('span');
                element.textContent = value;
                return element.innerHTML;
            }

            canvas.addEventListener('pointerdown', event => {
                const element = event.target.closest('.seat-map-object');
                if (!element) return;
                const object = objects.find(item => item.id === Number(element.dataset.id));
                if (!object) return;
                selectedId = object.id;
                const action = event.target.dataset.action || 'move';
                interaction = { action, startX: event.clientX, startY: event.clientY, x: object.x, y: object.y, width: object.width, height: object.height, rotation: object.rotation || 0 };
                element.setPointerCapture?.(event.pointerId);
                render();
            });

            window.addEventListener('pointermove', event => {
                if (!interaction) return;
                const object = selected();
                if (!object) return;
                const dx = event.clientX - interaction.startX;
                const dy = event.clientY - interaction.startY;
                if (interaction.action === 'resize') {
                    object.width = Math.max(80, interaction.width + dx);
                    object.height = Math.max(45, interaction.height + dy);
                } else if (interaction.action === 'rotate') {
                    object.rotation = interaction.rotation + Math.round(dy / 10) * 5;
                } else {
                    object.x = Math.max(0, interaction.x + dx);
                    object.y = Math.max(0, interaction.y + dy);
                }
                render();
            });

            window.addEventListener('pointerup', () => { interaction = null; });

            canvas.addEventListener('click', event => {
                const element = event.target.closest('.seat-map-object');
                if (element) {
                    selectedId = Number(element.dataset.id);
                    render();
                }
            });

            document.querySelectorAll('[data-add]').forEach(button => button.addEventListener('click', () => createObject(button.dataset.add)));
            document.getElementById('add-zone').addEventListener('click', () => {
                createObject('zone', { name: document.getElementById('new-zone-name').value || 'A', count: Number(document.getElementById('new-zone-count').value) || 1, price: Number(document.getElementById('new-zone-price').value) || 0, zone: document.getElementById('new-zone-name').value || 'A', width: 240, height: 170 });
            });

            document.getElementById('edit-name').addEventListener('input', event => { const object = selected(); if (object) { object.name = event.target.value; render(); } });
            document.getElementById('edit-count').addEventListener('input', event => { const object = selected(); if (object) { object.count = Number(event.target.value) || 1; render(); } });
            document.getElementById('edit-price').addEventListener('input', event => { const object = selected(); if (object) { object.price = Number(event.target.value) || 0; render(); } });
            document.getElementById('delete-object').addEventListener('click', () => { objects = objects.filter(object => object.id !== selectedId); selectedId = null; render(); });

            document.getElementById('save-map-form').addEventListener('submit', () => {
                const rows = objects.filter(object => object.type === 'zone').map(object => ({ name: object.name, seats: Number(object.count || 0), zone: object.zone || object.name, price: Number(object.price || 0), ticket_type_id: Number(object.ticket_type_id || 0) }));
                document.getElementById('layout-input').value = JSON.stringify({ version: 2, canvas: { width: canvas.offsetWidth, height: canvas.offsetHeight }, objects, rows });
            });

            render();
        })();
    </script>

    <style>
        .seat-map-object {
            position: absolute;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: flex-start;
            gap: 8px;
            padding: 12px;
            user-select: none;
            cursor: move;
            transform-origin: center;
        }

        .seat-map-selected {
            outline: 2px dashed #2563eb;
            outline-offset: 4px;
        }

        .seat-map-stage {
            justify-content: center;
            border-radius: 8px;
            background: #111827;
            color: white;
        }

        .seat-map-entrance {
            justify-content: center;
            border: 3px solid #16a34a;
            border-radius: 6px;
            background: #dcfce7;
            color: #166534;
        }

        .seat-map-exit {
            justify-content: center;
            border: 3px solid #dc2626;
            border-radius: 6px;
            background: #fee2e2;
            color: #991b1b;
        }

        .seat-map-text {
            justify-content: center;
            border: 1px dashed #94a3b8;
            background: #f8fafc;
        }

        .seat-map-zone {
            border: 2px solid #374151;
            border-radius: 8px;
            background: #fff;
        }

        .seat-map-seats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(22px, 1fr));
            width: 100%;
            gap: 4px;
            overflow: hidden;
        }

        .seat-map-seats span {
            display: grid;
            height: 22px;
            place-items: center;
            border: 1px solid #94a3b8;
            border-radius: 4px;
            background: #e5e7eb;
            color: #374151;
            font-size: 10px;
        }

        .seat-map-capacity {
            display: grid;
            min-height: 50px;
            width: 100%;
            place-items: center;
            border: 1px dashed #64748b;
            border-radius: 6px;
            background: #f8fafc;
            color: #475569;
            font-weight: 600;
        }

        .seat-map-resize,
        .seat-map-rotate {
            position: absolute;
            z-index: 2;
            height: 14px;
            width: 14px;
            border: 2px solid white;
            border-radius: 999px;
        }

        .seat-map-resize {
            right: -8px;
            bottom: -8px;
            cursor: nwse-resize;
            background: #2563eb;
        }

        .seat-map-rotate {
            top: -26px;
            left: calc(50% - 7px);
            cursor: grab;
            background: #16a34a;
        }
    </style>
@endsection