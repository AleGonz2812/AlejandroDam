import tkinter as tk
from tkinter import messagebox
import random
from db import crear_tabla, guardar_puntuacion, obtener_puntuaciones

# Crear tabla al iniciar
crear_tabla()

# Lista de películas con pistas
peliculas = [
    {
        "nombre": "Titanic",
        "pistas": [
            "Una historia de amor trágica.",
            "Basada en un evento real.",
            "Protagonizada por Leonardo DiCaprio y Kate Winslet."
        ]
    },
    {
        "nombre": "Breaking Bad",
        "pistas": [
            "Serie sobre un profesor de química.",
            "Trata sobre la fabricación de drogas.",
            "Protagonizada por Bryan Cranston."
        ]
    },
    {
        "nombre": "Toy Story",
        "pistas": [
            "Una película animada.",
            "Los protagonistas son juguetes.",
            "Producida por Pixar."
        ]
    }
]

class JuegoAdivina:
    def __init__(self, root):
        self.root = root
        self.root.title("🎬 Adivina la Película o Serie 🎬")
        self.root.geometry("500x400")

        self.jugador = ""
        self.pelicula_actual = {}
        self.pistas_mostradas = 0
        self.puntos = 30

        self.inicio()

    def inicio(self):
        for widget in self.root.winfo_children():
            widget.destroy()

        tk.Label(self.root, text="Introduce tu nombre:", font=("Arial", 14)).pack(pady=20)
        self.nombre_entry = tk.Entry(self.root, font=("Arial", 12))
        self.nombre_entry.pack()

        tk.Button(self.root, text="Comenzar juego", command=self.empezar_juego).pack(pady=10)
        tk.Button(self.root, text="Ver puntuaciones", command=self.mostrar_puntuaciones).pack()

    def empezar_juego(self):
        self.jugador = self.nombre_entry.get().strip()
        if not self.jugador:
            messagebox.showwarning("Aviso", "Debes introducir tu nombre.")
            return

        self.pelicula_actual = random.choice(peliculas)
        self.pistas_mostradas = 0
        self.puntos = 30
        self.mostrar_juego()

    def mostrar_juego(self):
        for widget in self.root.winfo_children():
            widget.destroy()

        tk.Label(self.root, text=f"Jugador: {self.jugador} | Puntos: {self.puntos}", font=("Arial", 12)).pack(pady=5)
        tk.Label(self.root, text="Adivina la película o serie:", font=("Arial", 14)).pack(pady=10)

        self.pista_label = tk.Label(self.root, text="", font=("Arial", 12), wraplength=400)
        self.pista_label.pack(pady=10)

        tk.Button(self.root, text="Pedir pista (-5 pts)", command=self.mostrar_pista).pack(pady=5)

        self.respuesta_entry = tk.Entry(self.root, font=("Arial", 12))
        self.respuesta_entry.pack(pady=10)

        tk.Button(self.root, text="Comprobar respuesta", command=self.comprobar_respuesta).pack(pady=5)
        tk.Button(self.root, text="Volver al inicio", command=self.inicio).pack(pady=10)

    def mostrar_pista(self):
        if self.pistas_mostradas < len(self.pelicula_actual["pistas"]):
            self.pista_label.config(text=self.pelicula_actual["pistas"][self.pistas_mostradas])
            self.pistas_mostradas += 1
            self.puntos -= 5
        else:
            messagebox.showinfo("Sin pistas", "Ya has visto todas las pistas.")

    def comprobar_respuesta(self):
        respuesta = self.respuesta_entry.get().strip().lower()
        correcto = self.pelicula_actual["nombre"].lower()

        if respuesta == correcto:
            messagebox.showinfo("¡Correcto!", f"¡Adivinaste! Has obtenido {self.puntos} puntos.")
            guardar_puntuacion(self.jugador, self.pelicula_actual["nombre"], self.puntos)
            self.inicio()
        else:
            messagebox.showerror("Incorrecto", "Esa no es la respuesta correcta. ¡Sigue intentando!")

    def mostrar_puntuaciones(self):
        for widget in self.root.winfo_children():
            widget.destroy()

        tk.Label(self.root, text="🏆 Puntuaciones más altas 🏆", font=("Arial", 14)).pack(pady=10)
        puntuaciones = obtener_puntuaciones()

        if not puntuaciones:
            tk.Label(self.root, text="No hay puntuaciones registradas aún.").pack()
        else:
            for jugador, pelicula, puntos in puntuaciones:
                tk.Label(self.root, text=f"{jugador} - {pelicula} ({puntos} pts)", font=("Arial", 11)).pack()

        tk.Button(self.root, text="Volver", command=self.inicio).pack(pady=10)


if __name__ == "__main__":
    root = tk.Tk()
    app = JuegoAdivina(root)
    root.mainloop()
