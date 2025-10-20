import java.util.LinkedList;
import java.util.Queue;
import java.util.Scanner;
import java.util.Stack;

public class Ej3 {
    public static void main(String[] args) {
        Scanner sc = new Scanner(System.in);
        Stack<Character> pila = new Stack<>();
        Queue<Character> cola = new LinkedList<>();

        System.out.print("Introduce la frase: ");
        String frase = sc.nextLine().toLowerCase().replaceAll(" ", "");

        for (char c : frase.toCharArray()) {
            pila.push(c);
            cola.add(c);
        }

        boolean esPalindromo = true;
        while (!pila.isEmpty()) {
            if (!pila.pop().equals(cola.poll())) {
                esPalindromo = false;
                break;
            }
        }

        System.out.println(esPalindromo ? "Es palíndromo" : "No es palíndromo");
    }
}
