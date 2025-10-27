package Ej2;

public class Contacto implements Comparable<Contacto> {
    String nombre, apellidos, telefono, email;

    public Contacto(String nombre, String apellidos, String telefono, String email) {
        this.nombre = nombre;
        this.apellidos = apellidos;
        this.telefono = telefono;
        this.email = email;
    }

    @Override
    public int compareTo(Contacto o) {
        int cmp = apellidos.compareToIgnoreCase(o.apellidos);
        return (cmp != 0) ? cmp : nombre.compareToIgnoreCase(o.nombre);
    }

    @Override
    public String toString() {
        return apellidos + ", " + nombre + " - " + telefono +
                (email != null && !email.isEmpty() ? " (" + email + ")" : "");
    }
}