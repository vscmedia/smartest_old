module("Control.Window");

test("Basic requirements", function () {
    expect(1);
    ok(Control.Window, "Control.Window");
});

test("draggable", function () {
    expect(3);
    ok(Draggables, "Draggables");

    var w = new Control.Window("window", { draggable : true });

    equals(Draggables.observers.length, 1, "1 draggable observer is present after initialization");
    w.destroy();
    equals(Draggables.observers.length, 0, "0 draggable observers are present after the last one is destroyed");
}); 

test("overlay z-index", function() {
    expect(4);
    var setZto = 99999999;
    
    equals(Control.Window.baseZIndex, 9999, "Z-index intitial value is set");
    Control.Window.baseZIndex = setZto;
    equals(Control.Window.baseZIndex, setZto, "Z-index accepted value set");

    modal = new Control.Modal();
    modal.open();

    // these next two should pass just fine in all browsers,
    // but on non ie touches both possible code paths
    equals(Control.Overlay.getIeStyles().zIndex, setZto - 1 , "Overlay inherited z-index from Window for ieStyles");
    equals($('control_overlay').getStyles().zIndex, (String) (setZto - 1), "Overlay inherited z-index from Window for styles")
});