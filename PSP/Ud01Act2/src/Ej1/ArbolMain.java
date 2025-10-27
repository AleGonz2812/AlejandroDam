package Ej1;

import java.util.Scanner;

public class ArbolMain {
    public static void main(String[] args) {
        Scanner sc = new Scanner(System.in);
        ArbolBinario arbol = new ArbolBinario();
        arbol.construirEjemplo();

        System.out.print("Introduce una letra a buscar: ");
        String entrada = sc.nextLine().trim().toUpperCase();

        if (entrada.isEmpty()) {
            System.out.println("Entrada vacía.");
            return;
        }

        char objetivo = entrada.charAt(0);
        StringBuilder letras = new StringBuilder();
        StringBuilder direcciones = new StringBuilder();

        if (arbol.buscarCamino(arbol.raiz, objetivo, letras, direcciones)) {
            System.out.println("Camino encontrado: " + letras);
            System.out.println("Direcciones: " + direcciones);
        } else {
            System.out.println("La letra " + objetivo + " no se encuentra en el árbol.");
        }

        System.out.println("\nÁrbol por niveles:");
        arbol.mostrarPorNiveles();
    }
}
