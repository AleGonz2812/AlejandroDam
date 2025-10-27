package Ej2;

public class ListinMain {
    public static void main(String[] args) {
        ArbolContactos listin = new ArbolContactos();

        listin.insertar(new Contacto("Ana", "García", "600111222", "ana@gmail.com"));
        listin.insertar(new Contacto("Luis", "Alonso", "600333444", "luis@correo.com"));
        listin.insertar(new Contacto("María", "Zapata", "600555666", ""));
        listin.insertar(new Contacto("Pedro", "García", "600777888", "pedro@empresa.com"));

        System.out.println("Listado de contactos (in-orden):");
        listin.mostrarInOrden();

        System.out.println();
        listin.buscarPorPrefijo("Gar");
    }
}

