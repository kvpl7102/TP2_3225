import mysql.connector
import json

with open('facts.json', 'r') as f:
    facts = json.load(f)

# Connect to MySQL server
servername = "localhost"
username = "root"
password = ""

db = mysql.connector.connect(
    host=servername,
    user=username,
    password=password
)

cursor = db.cursor()

# Create database
cursor.execute("DROP DATABASE IF EXISTS ConceptNetDB")
cursor.execute("CREATE DATABASE IF NOT EXISTS ConceptNetDB")

# Use the new database
cursor.execute("USE ConceptNetDB")

# --------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
# Create table for facts
cursor.execute("""
    CREATE TABLE IF NOT EXISTS Facts (
        idFact VARCHAR(255),
        start VARCHAR(255),
        relation VARCHAR(255),
        end VARCHAR(255)
    )
""")

# Insert into the table facts
for fact in facts:
    idFact = fact['idFact']
    start = fact['start']
    relation = fact['relation']
    end = fact['end']
    cursor.execute("INSERT INTO Facts (idFact, start, relation, end) VALUES (%s, %s, %s,%s)", (idFact, start, relation, end))

# --------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
# Create table for users
cursor.execute("""
    CREATE TABLE IF NOT EXISTS Users (
        idUser INT AUTO_INCREMENT PRIMARY KEY,
        username VARCHAR(255) UNIQUE NOT NULL,
        password VARCHAR(255) NOT NULL
    )
""")

# Insert into the table users
cursor.execute("INSERT INTO Users (username, password) VALUES ('ift3225', '5223tfi')")
cursor.execute("INSERT INTO Users (username, password) VALUES ('user', 'userpassword')")
cursor.execute("INSERT INTO Users (username, password) VALUES ('guest', 'guestpassword')")
cursor.execute("INSERT INTO Users (username, password) VALUES ('test1', 'test1password')")
cursor.execute("INSERT INTO Users (username, password) VALUES ('test2', 'test2password')")

# --------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
# Commit changes and close connection
db.commit()
cursor.close()
db.close()