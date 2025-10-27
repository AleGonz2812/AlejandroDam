package Ej3;

import java.util.*;

public class RutasMain {
    public static void main(String[] args) {
        Grafo ciudad = new Grafo();

        ciudad.agregarConexion("A", "B", 5, "peatonal");
        ciudad.agregarConexion("A", "C", 2, "bici");
        ciudad.agregarConexion("B", "D", 3, "bus");
        ciudad.agregarConexion("C", "D", 6, "metro");
        ciudad.agregarConexion("B", "E", 4, "bici");
        ciudad.agregarConexion("D", "E", 2, "peatonal");

        Scanner sc = new Scanner(System.in);
        System.out.print("Inicio: ");
        String inicio = sc.nextLine().toUpperCase();

        System.out.print("Destino: ");
        String fin = sc.nextLine().toUpperCase();

        System.out.print("¿Deseas evitar algún transporte (bici, metro, bus, peatonal)? (separados por comas o vacío): ");
        String entrada = sc.nextLine().trim().toLowerCase();

        Set<String> evitar = new HashSet<>();
        if (!entrada.isEmpty()) evitar.addAll(Arrays.asList(entrada.split(",")));

        ciudad.dijkstra(inicio, fin, evitar);
    }
}
