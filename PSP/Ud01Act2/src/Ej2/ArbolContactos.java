package Ej2;

public class ArbolContactos {
    NodoContacto raiz;

    public void insertar(Contacto c) {
        raiz = insertarRec(raiz, c);
    }

    private NodoContacto insertarRec(NodoContacto n, Contacto c) {
        if (n == null) return new NodoContacto(c);
        if (c.compareTo(n.c) < 0) n.izq = insertarRec(n.izq, c);
        else n.der = insertarRec(n.der, c);
        return n;
    }

    public void mostrarInOrden() {
        mostrarRec(raiz);
    }

    private void mostrarRec(NodoContacto n) {
        if (n == null) return;
        mostrarRec(n.izq);
        System.out.println(n.c);
        mostrarRec(n.der);
    }

    public void buscarPorPrefijo(String prefijo) {
        System.out.println("Contactos con prefijo \"" + prefijo + "\":");
        buscarPrefijoRec(raiz, prefijo.toLowerCase());
    }

    private void buscarPrefijoRec(NodoContacto n, String prefijo) {
        if (n == null) return;
        if (n.c.apellidos.toLowerCase().startsWith(prefijo)) System.out.println(n.c);
        buscarPrefijoRec(n.izq, prefijo);
        buscarPrefijoRec(n.der, prefijo);
    }
}
