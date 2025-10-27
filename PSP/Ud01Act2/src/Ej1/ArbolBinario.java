package Ej1;

import java.util.*;

public class ArbolBinario {
    Nodo raiz;

    public void construirEjemplo() {
        raiz = new Nodo('A');
        raiz.izquierda = new Nodo('B');
        raiz.derecha = new Nodo('C');
        raiz.izquierda.izquierda = new Nodo('D');
        raiz.izquierda.derecha = new Nodo('E');
        raiz.derecha.izquierda = new Nodo('F');
        raiz.derecha.derecha = new Nodo('G');
    }

    public boolean buscarCamino(Nodo actual, char objetivo, StringBuilder letras, StringBuilder direcciones) {
        if (actual == null) return false;

        letras.append(actual.valor).append(" ");
        if (actual.valor == objetivo) return true;

        direcciones.append("L");
        if (buscarCamino(actual.izquierda, objetivo, letras, direcciones)) return true;
        direcciones.deleteCharAt(direcciones.length() - 1);

        direcciones.append("R");
        if (buscarCamino(actual.derecha, objetivo, letras, direcciones)) return true;
        direcciones.deleteCharAt(direcciones.length() - 1);

        return false;
    }

    public void mostrarPorNiveles() {
        if (raiz == null) return;
        Queue<Nodo> cola = new LinkedList<>();
        cola.add(raiz);

        while (!cola.isEmpty()) {
            Nodo actual = cola.poll();
            System.out.print(actual.valor + " ");
            if (actual.izquierda != null) cola.add(actual.izquierda);
            if (actual.derecha != null) cola.add(actual.derecha);
        }
        System.out.println();
    }
}
