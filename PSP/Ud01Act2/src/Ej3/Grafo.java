package Ej3;

import java.util.*;

public class Grafo {
    Map<String, List<Artista>> adj = new HashMap<>();

    public void agregarConexion(String origen, String destino, double tiempo, String tipo) {
        adj.computeIfAbsent(origen, k -> new ArrayList<>()).add(new Artista(destino, tiempo, tipo));
    }

    public void dijkstra(String inicio, String fin, Set<String> evitarTipos) {
        Map<String, Double> dist = new HashMap<>();
        Map<String, String> prev = new HashMap<>();
        PriorityQueue<String> pq = new PriorityQueue<>(Comparator.comparingDouble(dist::get));

        for (String nodo : adj.keySet()) dist.put(nodo, Double.MAX_VALUE);
        dist.put(inicio, 0.0);
        pq.add(inicio);

        while (!pq.isEmpty()) {
            String actual = pq.poll();
            if (actual.equals(fin)) break;

            for (Artista a : adj.getOrDefault(actual, List.of())) {
                if (evitarTipos.contains(a.tipo)) continue;
                double nuevaDist = dist.get(actual) + a.tiempo;
                if (nuevaDist < dist.getOrDefault(a.destino, Double.MAX_VALUE)) {
                    dist.put(a.destino, nuevaDist);
                    prev.put(a.destino, actual);
                    pq.add(a.destino);
                }
            }
        }

        if (!prev.containsKey(fin)) {
            System.out.println("No hay ruta posible respetando las restricciones.");
            return;
        }

        List<String> camino = new ArrayList<>();
        for (String at = fin; at != null; at = prev.get(at)) camino.add(at);
        Collections.reverse(camino);

        System.out.println("Ruta óptima: " + camino);
        System.out.println("Tiempo total: " + dist.get(fin) + " minutos");
    }
}
