import java.util.Scanner;
import java.util.Stack;

public class Ej4 {
    public static void main(String[] args) {
        Scanner sc = new Scanner(System.in);
        Stack<Integer> pila = new Stack<>();

        System.out.print("Introduce un número entero positivo: ");
        int numero = sc.nextInt();

        while (numero > 0) {
            pila.push(numero % 2); // resto
            numero /= 2;
        }

        System.out.print("Binario: ");
        while (!pila.isEmpty()) {
            System.out.print(pila.pop());
        }
    }
}
