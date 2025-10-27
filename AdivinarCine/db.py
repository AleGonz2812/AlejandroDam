import sqlite3

def crear_tabla():
    conn = sqlite3.connect("peliculas.db")
    c = conn.cursor()
    c.execute('''
        CREATE TABLE IF NOT EXISTS puntuaciones (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            jugador TEXT NOT NULL,
            pelicula TEXT NOT NULL,
            puntos INTEGER NOT NULL
        )
    ''')
    conn.commit()
    conn.close()

def guardar_puntuacion(jugador, pelicula, puntos):
    conn = sqlite3.connect("peliculas.db")
    c = conn.cursor()
    c.execute("INSERT INTO puntuaciones (jugador, pelicula, puntos) VALUES (?, ?, ?)",
              (jugador, pelicula, puntos))
    conn.commit()
    conn.close()

def obtener_puntuaciones():
    conn = sqlite3.connect("peliculas.db")
    c = conn.cursor()
    c.execute("SELECT jugador, pelicula, puntos FROM puntuaciones ORDER BY puntos DESC LIMIT 10")
    datos = c.fetchall()
    conn.close()
    return datos
