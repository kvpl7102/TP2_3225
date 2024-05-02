import mysql.connector
import json

with open('facts.json', 'r') as f:
    facts = json.load(f)

# Connect to MySQL server
db = mysql.connector.connect(
    host="localhost",
    user="your_username",
    password="your_password"
)

cursor = db.cursor()

# Create database
cursor.execute("CREATE DATABASE IF NOT EXISTS ConceptNetDB")

# Use the new database
cursor.execute("USE ConceptNetDB")

# Create table for facts
cursor.execute("""
    CREATE TABLE IF NOT EXISTS Facts (
        id INT AUTO_INCREMENT PRIMARY KEY,
        start VARCHAR(255),
        relation VARCHAR(255),
        end VARCHAR(255)
    )
""")

# Insert facts into the table
for fact in facts:
    start = fact['start']
    relation = fact['rel']
    end = fact['end']
    cursor.execute("INSERT INTO Facts (start, relation, end) VALUES (%s, %s, %s)", (start, relation, end))

# Commit changes and close connection
db.commit()
cursor.close()
db.close()