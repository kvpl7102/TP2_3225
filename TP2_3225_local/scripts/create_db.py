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

# Create table for facts
cursor.execute("""
    CREATE TABLE IF NOT EXISTS Facts (
        idFact VARCHAR(255),
        start VARCHAR(255),
        relation VARCHAR(255),
        end VARCHAR(255)
    )
""")

# Insert facts into the table
for fact in facts:
    idFact = fact['idFact']
    start = fact['start']
    relation = fact['relation']
    end = fact['end']
    cursor.execute("INSERT INTO Facts (idFact, start, relation, end) VALUES (%s, %s, %s,%s)", (idFact, start, relation, end))

# Commit changes and close connection
db.commit()
cursor.close()
db.close()