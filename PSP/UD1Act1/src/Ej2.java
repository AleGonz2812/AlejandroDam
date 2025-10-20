import java.util.LinkedList;
import java.util.Queue;
import java.util.Scanner;

public class Ej2 {
    public static void main(String[] args) {
        Scanner sc = new Scanner(System.in);
        Queue<String> cola = new LinkedList<>();
        int opcion;

        do {
            System.out.println("\n1) Llegada\n2) Atender\n3) Mostrar cola\n4) Salir");
            System.out.print("Elige una opción: ");
            opcion = sc.nextInt();
            sc.nextLine(); // limpiar buffer

            switch (opcion) {
                case 1:
                    System.out.print("Nombre del cliente: ");
                    String nombre = sc.nextLine();
                    cola.add(nombre); // ENCOLAR
                    break;
                case 2:
                    if (!cola.isEmpty()) {
                        System.out.println("Atendido: " + cola.poll()); // DESENCOLAR
                    } else {
                        System.out.println("No hay clientes.");
                    }
                    break;
                case 3:
                    System.out.println("Clientes en cola: " + cola);
                    break;
                case 4:
                    System.out.println("Saliendo..." +
                            " Gracias por usar el sistema.");
                    break;
                default:
                    System.out.println("Opción inválida.");
            }
        } while (opcion != 4);
    }
}
