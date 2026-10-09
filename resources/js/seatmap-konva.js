import Konva from 'konva';

class SeatMapKonva {
    constructor(config) {
        this.containerId = config.containerId;
        this.container = document.getElementById(this.containerId);
        
        let layout = config.layout || {};
        if (typeof layout === 'string') {
            try { layout = JSON.parse(layout); } catch (e) {}
        }
        this.layout = layout;
        
        this.mode = config.mode || 'view'; // 'edit' or 'view'
        this.isAssignedSeat = config.isAssignedSeat || false;
        
        let rawObjects = this.layout?.objects || [];
        this.objects = Array.isArray(rawObjects) ? rawObjects : Object.values(rawObjects);
        
        this.seatStates = config.seatStates || {}; 
        this.highlightedZone = null;
        
        this.onSelect = config.onSelect || (() => {});
        this.onSeatClick = config.onSeatClick || (() => {});
        this.onZoneClick = config.onZoneClick || (() => {});
        this.onUpdate = config.onUpdate || (() => {});

        this.colors = {
            stage: { bg: '#27272a', stroke: '#3f3f46', text: '#ffffff' },
            entrance: { bg: 'rgba(22, 163, 74, 0.1)', stroke: '#16a34a', text: '#4ade80' },
            exit: { bg: 'rgba(220, 38, 38, 0.1)', stroke: '#dc2626', text: '#f87171' },
            text: { bg: 'transparent', stroke: 'transparent', text: '#a1a1aa' },
            zone: { bg: '#18181b', stroke: '#3f3f46', text: '#ffffff' },
            zoneHighlight: { bg: '#1e3a8a', stroke: '#3b82f6', text: '#ffffff' },
            seat: { 
                available: { bg: '#3f3f46', stroke: '#52525b', text: '#d4d4d8' },
                selected: { bg: '#3b82f6', stroke: '#60a5fa', text: '#ffffff' },
                held: { bg: '#f97316', stroke: '#fdba74', text: '#ffffff' },
                sold: { bg: '#ef4444', stroke: '#fca5a5', text: '#ffffff', opacity: 0.7 }
            }
        };

        // Initialize Core Modules
        this.initViewport();
        this.initRenderer();
        if (this.mode === 'edit') {
            this.initEditor();
        } else {
            this.initBooking();
        }

        // Initial Render & Fit
        this.renderAll();
        setTimeout(() => this.fitToScreen(), 50);

        // Auto-fit on window resize
        window.addEventListener('resize', () => {
            if (this.container && this.stage) {
                this.stage.width(this.container.offsetWidth);
                this.stage.height(this.container.offsetHeight);
                this.fitToScreen();
            }
        });
    }

    // ==========================================
    // 1. VIEWPORT (Pan, Zoom, Fit)
    // ==========================================
    initViewport() {
        Konva.dragDistance = 5; // Prevent accidental drags when clicking

        this.stage = new Konva.Stage({
            container: this.containerId,
            width: this.container.offsetWidth,
            height: this.container.offsetHeight,
            draggable: this.mode === 'view' // Only allow stage dragging in View mode
        });

        // Wheel Zoom to Pointer
        this.stage.on('wheel', (e) => {
            e.evt.preventDefault();
            const scaleBy = 1.1;
            const oldScale = this.stage.scaleX();
            const pointer = this.stage.getPointerPosition();

            const mousePointTo = {
                x: (pointer.x - this.stage.x()) / oldScale,
                y: (pointer.y - this.stage.y()) / oldScale,
            };

            let newScale = e.evt.deltaY > 0 ? oldScale / scaleBy : oldScale * scaleBy;
            newScale = Math.max(0.2, Math.min(newScale, 5));

            this.stage.scale({ x: newScale, y: newScale });
            const newPos = {
                x: pointer.x - mousePointTo.x * newScale,
                y: pointer.y - mousePointTo.y * newScale,
            };
            this.stage.position(newPos);
            this.clampPan();
        });

        // Clamp Pan bounds
        this.stage.on('dragmove', () => {
            this.clampPan();
        });

        // Cursor styles for Pan
        if (this.mode === 'view') {
            this.container.style.cursor = 'grab';
            this.stage.on('mousedown touchstart', (e) => {
                if (e.target === this.stage) this.container.style.cursor = 'grabbing';
            });
            this.stage.on('mouseup touchend', () => {
                this.container.style.cursor = 'grab';
            });
        }
    }

    clampPan() {
        const vw = this.stage.width();
        const vh = this.stage.height();
        const cw = (this.layout.canvas?.width || 1400) * this.stage.scaleX();
        const ch = (this.layout.canvas?.height || 850) * this.stage.scaleY();
        
        const padX = Math.min(200, vw / 2);
        const padY = Math.min(200, vh / 2);
        
        const minX = padX - cw;
        const maxX = vw - padX;
        const minY = padY - ch;
        const maxY = vh - padY;
        
        let x = this.stage.x();
        let y = this.stage.y();
        
        x = Math.max(minX, Math.min(maxX, x));
        y = Math.max(minY, Math.min(maxY, y));
        
        this.stage.position({ x, y });
        this.stage.batchDraw();
    }

    fitToScreen() {
        const cw = this.layout.canvas?.width || 1400;
        const ch = this.layout.canvas?.height || 850;
        const vw = this.stage.width();
        const vh = this.stage.height();
        
        const scale = Math.min(vw / cw, vh / ch) * 0.9;
        this.stage.scale({ x: scale, y: scale });
        this.stage.position({
            x: (vw - cw * scale) / 2,
            y: (vh - ch * scale) / 2
        });
        this.clampPan();
        this.stage.batchDraw();
    }
    
    zoomIn() {
        this.zoomToCenter(Math.min(this.stage.scaleX() * 1.2, 5));
    }

    zoomOut() {
        this.zoomToCenter(Math.max(this.stage.scaleX() / 1.2, 0.2));
    }
    
    zoomToCenter(newScale) {
        const center = { x: this.stage.width() / 2, y: this.stage.height() / 2 };
        const oldScale = this.stage.scaleX();
        const mousePointTo = {
            x: (center.x - this.stage.x()) / oldScale,
            y: (center.y - this.stage.y()) / oldScale,
        };
        this.stage.scale({ x: newScale, y: newScale });
        this.stage.position({
            x: center.x - mousePointTo.x * newScale,
            y: center.y - mousePointTo.y * newScale,
        });
        this.clampPan();
    }

    // ==========================================
    // 2. RENDERER (Render JSON to Konva)
    // ==========================================
    initRenderer() {
        this.layer = new Konva.Layer();
        this.stage.add(this.layer);
    }
    
    renderAll() {
        // Destroy existing object groups
        this.layer.getChildren(node => node.hasName('object-group')).forEach(n => n.destroy());

        this.objects.forEach(obj => {
            const group = new Konva.Group({
                id: String(obj.id),
                name: 'object-group',
                x: obj.x,
                y: obj.y,
                width: obj.width,
                height: obj.height,
                rotation: obj.rotation || 0,
                draggable: this.mode === 'edit'
            });

            const isZone = obj.type === 'zone';
            const isHighlight = this.highlightedZone === obj.name;
            const theme = isZone ? (isHighlight ? this.colors.zoneHighlight : this.colors.zone) : (this.colors[obj.type] || this.colors.text);

            // Base Background
            const bgRect = new Konva.Rect({
                name: 'bg-rect',
                width: obj.width,
                height: obj.height,
                fill: this.mode === 'edit' ? (isZone ? '#ffffff' : theme.bg) : theme.bg,
                stroke: this.mode === 'edit' ? (isZone ? '#374151' : theme.stroke) : theme.stroke,
                strokeWidth: 2,
                cornerRadius: (obj.type === 'stage' || obj.type === 'zone') ? 8 : (obj.type === 'text' ? 0 : 6),
                dash: obj.type === 'text' ? [5, 5] : []
            });
            
            if (this.mode === 'edit' && obj.type === 'text') {
                bgRect.stroke('#94a3b8');
                bgRect.fill('#f8fafc');
            }
            group.add(bgRect);

            // Title
            let titleText = obj.name;
            if (this.mode === 'view' && isZone && obj.price) {
                titleText += ` - ${new Intl.NumberFormat('vi-VN').format(obj.price)}đ`;
            }
            
            const titleHeight = 24;
            const title = new Konva.Text({
                text: titleText,
                width: obj.width,
                height: titleHeight,
                align: 'center',
                verticalAlign: 'middle',
                y: 8,
                fontSize: 14,
                fontStyle: 'bold',
                fill: this.mode === 'edit' && isZone ? '#000000' : theme.text
            });
            group.add(title);

            // Seats or General Admission text
            if (isZone) {
                if (this.isAssignedSeat) {
                    this.drawSeats(group, obj, titleHeight);
                } else {
                    const capacityText = new Konva.Text({
                        text: `${obj.count || 0} vé`,
                        width: obj.width,
                        height: obj.height - titleHeight - 16,
                        y: titleHeight + 8,
                        align: 'center',
                        verticalAlign: 'middle',
                        fontSize: 14,
                        fontStyle: 'bold',
                        fill: this.mode === 'edit' ? '#475569' : '#ffffff'
                    });
                    group.add(capacityText);
                    
                    if (this.mode === 'view') {
                        group.on('click tap', (e) => {
                            if (this.stage.isDragging()) return;
                            this.onZoneClick(obj);
                        });
                        group.on('mouseenter', () => document.body.style.cursor = 'pointer');
                        group.on('mouseleave', () => document.body.style.cursor = 'default');
                    }
                }
            }

            // Editor Events
            if (this.mode === 'edit') {
                group.on('mousedown touchstart', (e) => {
                    this.selectObject(obj.id);
                    e.cancelBubble = true; // Prevent dragging stage (though stage isn't draggable)
                });
                group.on('dragend', () => {
                    obj.x = group.x();
                    obj.y = group.y();
                    this.onUpdate();
                });
                group.on('transformend', () => {
                    obj.x = group.x();
                    obj.y = group.y();
                    obj.width = Math.max(20, group.width() * group.scaleX());
                    obj.height = Math.max(20, group.height() * group.scaleY());
                    obj.rotation = group.rotation();
                    
                    // Reset scale and apply to width/height instead for reflow
                    group.scaleX(1);
                    group.scaleY(1);
                    
                    this.onUpdate();
                    this.renderAll(); // Full render to reflow seats after resize
                });
            }

            this.layer.add(group);
        });

        // Restore selection order in Edit mode
        if (this.mode === 'edit' && this.selectedId) {
            this.selectObject(this.selectedId);
        } else if (this.transformer) {
            this.transformer.moveToTop();
        }
        
        this.layer.batchDraw();
    }

    drawSeats(group, obj, titleHeight) {
        const padding = 12;
        const availableWidth = obj.width - padding * 2;
        const availableHeight = obj.height - padding * 2 - titleHeight;
        const count = Number(obj.count || 0);
        const gap = 4;
        let bestSize = 0;
        
        if (availableWidth > 0 && availableHeight > 0 && count > 0) {
            let min = 1, max = Math.max(1, Math.min(availableWidth, availableHeight));
            while (min <= max) {
                let mid = Math.floor((min + max) / 2);
                let cols = Math.floor((availableWidth + gap) / (mid + gap));
                let rows = Math.floor((availableHeight + gap) / (mid + gap));
                if (cols * rows >= count) {
                    bestSize = mid;
                    min = mid + 1;
                } else {
                    max = mid - 1;
                }
            }
        }
        
        bestSize = Math.max(4, Math.min(bestSize, 40));
        let cols = Math.floor((availableWidth + gap) / (bestSize + gap));
        if (cols <= 0) cols = 1;
        
        const totalColsWidth = cols * bestSize + (cols - 1) * gap;
        const startX = padding + Math.max(0, (availableWidth - totalColsWidth) / 2);
        const startY = padding + titleHeight;
        
        for (let i = 0; i < count; i++) {
            const seatId = `${obj.name}${i + 1}`;
            const state = this.seatStates[seatId] || 'available';
            const theme = this.colors.seat[state];
            
            const row = Math.floor(i / cols);
            const col = i % cols;
            const x = startX + col * (bestSize + gap);
            const y = startY + row * (bestSize + gap);
            
            const seatGroup = new Konva.Group({ x, y, id: 'seat-' + seatId });
            
            const rect = new Konva.Rect({
                name: 'seat-rect',
                width: bestSize,
                height: bestSize,
                fill: this.mode === 'edit' ? '#e5e7eb' : theme.bg,
                stroke: this.mode === 'edit' ? '#94a3b8' : theme.stroke,
                strokeWidth: 1,
                cornerRadius: 2,
                opacity: theme.opacity || 1
            });
            seatGroup.add(rect);
            
            const fontSize = Math.max(5, bestSize * 0.45);
            const text = new Konva.Text({
                name: 'seat-text',
                text: seatId,
                width: bestSize,
                height: bestSize,
                align: 'center',
                verticalAlign: 'middle',
                fontSize: fontSize,
                fill: this.mode === 'edit' ? '#374151' : theme.text,
                opacity: theme.opacity || 1
            });
            seatGroup.add(text);
            
            // Seat Interactions (User only)
            if (this.mode === 'view') {
                this.attachSeatEvents(seatGroup, rect, seatId, obj);
            }
            
            group.add(seatGroup);
        }
    }

    // ==========================================
    // 3. EDITOR (Admin Drag, Resize, Rotate)
    // ==========================================
    initEditor() {
        this.transformer = new Konva.Transformer({
            nodes: [],
            padding: 0,
            enabledAnchors: ['top-left', 'top-right', 'bottom-left', 'bottom-right', 'top-center', 'bottom-center', 'middle-left', 'middle-right'],
            boundBoxFunc: (oldBox, newBox) => {
                if (newBox.width < 40 || newBox.height < 40) return oldBox;
                return newBox;
            }
        });
        this.layer.add(this.transformer);

        // Click on background deselects
        this.stage.on('mousedown touchstart', (e) => {
            if (e.target === this.stage) {
                this.selectObject(null);
            }
        });
    }

    selectObject(id) {
        this.selectedId = id;
        if (this.mode === 'edit') {
            if (id) {
                const node = this.layer.findOne('#' + id);
                if (node) {
                    this.transformer.nodes([node]);
                    node.moveToTop();
                    this.transformer.moveToTop();
                }
            } else {
                this.transformer.nodes([]);
            }
            this.layer.batchDraw();
        }
        
        const obj = id ? this.objects.find(o => String(o.id) === String(id)) : null;
        this.onSelect(obj);
    }

    // ==========================================
    // 4. BOOKING (User Click, State, Hover)
    // ==========================================
    initBooking() {
        // Any specific booking-wide inits could go here
    }

    attachSeatEvents(seatGroup, rect, seatId, obj) {
        let lastClickTime = 0;
        
        const updateHover = () => {
            const state = this.seatStates[seatId] || 'available';
            seatGroup.off('mouseenter mouseleave');
            
            if (state === 'available' || state === 'selected') {
                seatGroup.on('mouseenter', () => {
                    document.body.style.cursor = 'pointer';
                    if (this.seatStates[seatId] === 'available') {
                        rect.fill('#52525b');
                        rect.stroke('#a1a1aa');
                        this.layer.batchDraw();
                    }
                });
                seatGroup.on('mouseleave', () => {
                    document.body.style.cursor = 'default';
                    if (this.seatStates[seatId] === 'available') {
                        const theme = this.colors.seat.available;
                        rect.fill(theme.bg);
                        rect.stroke(theme.stroke);
                        this.layer.batchDraw();
                    }
                });
            } else {
                seatGroup.on('mouseenter', () => document.body.style.cursor = 'not-allowed');
                seatGroup.on('mouseleave', () => document.body.style.cursor = 'default');
            }
        };

        updateHover();
        
        seatGroup.on('click tap', (e) => {
            const now = Date.now();
            if (now - lastClickTime < 200) return; // Debounce
            lastClickTime = now;
            
            // Prevent synthetic double firing
            if (e.evt && e.evt.type === 'touchend') e.evt.preventDefault();
            
            if (this.stage.isDragging()) return; // Don't trigger if user just panned the map
            
            const currentState = this.seatStates[seatId] || 'available';
            if (currentState !== 'available' && currentState !== 'selected') return;

            this.onSeatClick(seatId, obj);
        });
    }

    updateSeatState(seatId, state) {
        this.seatStates[seatId] = state;
        const seatGroup = this.layer.findOne('#seat-' + seatId);
        
        if (seatGroup) {
            const rect = seatGroup.findOne('.seat-rect');
            const text = seatGroup.findOne('.seat-text');
            const theme = this.colors.seat[state] || this.colors.seat.available;
            
            if (rect) {
                rect.fill(theme.bg);
                rect.stroke(theme.stroke);
                rect.opacity(theme.opacity || 1);
            }
            if (text) {
                text.fill(theme.text);
                text.opacity(theme.opacity || 1);
            }
            
            // Re-attach hover logic based on new state
            if (this.mode === 'view') {
                this.attachSeatEvents(seatGroup, rect, seatId, null);
            }
            
            this.layer.batchDraw();
        }
    }
    
    highlightZone(zoneId) {
        this.highlightedZone = zoneId;
        this.renderAll(); // Zone highlighting requires a full re-render for simplicity
    }
}

window.SeatMapKonva = SeatMapKonva;
