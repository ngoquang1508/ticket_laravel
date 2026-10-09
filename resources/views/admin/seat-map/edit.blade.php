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

            <section class="min-w-0 flex-1 rounded-xl border bg-gray-100 p-3 shadow-sm">
                <div id="seat-map-canvas" class="relative w-full h-[75vh] min-h-[600px] rounded-lg border-2 border-gray-300 bg-white"
                    style="background-image: linear-gradient(#e5e7eb 1px, transparent 1px), linear-gradient(90deg, #e5e7eb 1px, transparent 1px); background-size: 25px 25px;">
                </div>
            </section>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const properties = document.getElementById('properties');
            const zoneProperties = document.getElementById('zone-properties');
            let initialLayout = @json($initialLayout);
            if (typeof initialLayout === 'string') {
                try { initialLayout = JSON.parse(initialLayout); } catch (e) { }
            }
            const isAssignedSeat = @json($event->sale_mode === 'assigned_seat');

            let objects = Array.isArray(initialLayout?.objects) ? initialLayout.objects : Object.values(initialLayout?.objects || {});
            let nextId = objects.reduce((max, object) => Math.max(max, Number(object.id) || 0), 0) + 1;

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
                initialLayout.objects = objects;
            }

            const map = new SeatMapKonva({
                containerId: 'seat-map-canvas',
                layout: initialLayout,
                mode: 'edit',
                isAssignedSeat: isAssignedSeat,
                onSelect: (object) => {
                    properties.classList.toggle('hidden', !object);
                    if (!object) return;
                    document.getElementById('edit-name').value = object.name || '';
                    zoneProperties.classList.toggle('hidden', object.type !== 'zone');
                    if (object.type === 'zone') {
                        document.getElementById('edit-count').value = object.count || 1;
                        document.getElementById('edit-price').value = object.price || 0;
                    }
                },
                onUpdate: () => {
                    // Update input fields on drag/resize
                    // Data is already updated in map.objects
                }
            });

            function createObject(type, values = {}) {
                const defaults = {
                    id: nextId++, type, name: type === 'stage' ? 'SÂN KHẤU' : type === 'entrance' ? 'LỐI VÀO' : type === 'exit' ? 'LỐI RA' : 'GHI CHÚ',
                    x: 120, y: 100, width: type === 'stage' ? 360 : 180, height: type === 'stage' ? 70 : 55, rotation: 0,
                };
                map.objects.push({ ...defaults, ...values });
                map.renderAll();
                map.selectObject(defaults.id);
            }

            document.querySelectorAll('[data-add]').forEach(button => button.addEventListener('click', () => createObject(button.dataset.add)));
            document.getElementById('add-zone').addEventListener('click', () => {
                createObject('zone', { name: document.getElementById('new-zone-name').value || 'A', count: Number(document.getElementById('new-zone-count').value) || 1, price: Number(document.getElementById('new-zone-price').value) || 0, zone: document.getElementById('new-zone-name').value || 'A', width: 240, height: 170 });
            });

            const updateSelected = (key, value) => {
                const object = map.objects.find(o => String(o.id) === String(map.selectedId));
                if (object) {
                    object[key] = value;
                    map.renderAll();
                }
            };

            document.getElementById('edit-name').addEventListener('input', event => updateSelected('name', event.target.value));
            document.getElementById('edit-count').addEventListener('input', event => updateSelected('count', Number(event.target.value) || 1));
            document.getElementById('edit-price').addEventListener('input', event => updateSelected('price', Number(event.target.value) || 0));
            document.getElementById('delete-object').addEventListener('click', () => {
                map.objects = map.objects.filter(object => String(object.id) !== String(map.selectedId));
                map.selectObject(null);
                map.renderAll();
            });

            document.getElementById('save-map-form').addEventListener('submit', () => {
                const rows = map.objects.filter(object => object.type === 'zone').map(object => ({ name: object.name, seats: Number(object.count || 0), zone: object.zone || object.name, price: Number(object.price || 0), ticket_type_id: Number(object.ticket_type_id || 0) }));

                // Keep original canvas width/height logic for compatibility
                const cw = initialLayout.canvas?.width || 1400;
                const ch = initialLayout.canvas?.height || 850;

                document.getElementById('layout-input').value = JSON.stringify({ version: 2, canvas: { width: cw, height: ch }, objects: map.objects, rows });
            });
        });
    </script>
@endsection