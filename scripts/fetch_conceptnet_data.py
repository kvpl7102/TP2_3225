import requests
import json
import random

def fetch_conceptnet_data(concept, lang):
    url = f"http://api.conceptnet.io/query?node=/c/{lang}/{concept}&other=/c/{lang}&limit=20"
    response = requests.get(url)
    data = response.json()
    return data

# List of concepts and relationships
concepts = {
    "Apple": "en",
    "Dog": "en",
    "Rain": "en",
    "Book": "en",
    "Tree": "en",
    "Car": "en",
    "Sun": "en",
    "Moon": "en",
    "Star": "en",
    "Ocean": "en",
    "Mountain": "en",
    "River": "en",
    "Flower": "en",
    "Bird": "en",
    "Cat": "en",
    "Fish": "en",
    "Butterfly": "en",
    "Leaf": "en",
    "Snow": "en",
    "Rainbow": "en",
    "Potato": "en",
    "Wolf": "en",
    "Wind": "en",
    "Chair": "en",
    "House": "en",
    "Boat": "en",
    "Earth": "en",
    "Cheese": "en",
    "Light": "en",
    "Forest": "en",
    "Beach": "en",
    "Lake": "en",
    "Hat": "en",
    "Lion": "en",
    "Mouse": "en",
    "Bird": "en",
    "Bee": "en",
    "Paper": "en",
    "Fire": "en",
    "Cloud": "en"
}


relations = ["IsA", "PartOf", "HasA", "UsedFor", "CapableOf", "AtLocation", "Causes", "HasProperty", "DefinedAs", "RelatedTo"]

facts = []


# Generate facts
for concept, lang in concepts.items():
    data = fetch_conceptnet_data(concept.lower(), lang)
    for edge in data['edges']:
        idFact = edge['@id']
        start = edge['start']['label']
        end = edge['end']['label']
        rel = edge['rel']['label']
        # relLabel = edge['rel']['label']
        
        if rel in relations:
            fact = {
                "idFact": idFact,
                "start": start,
                "relation": rel,
                "end": end
            }
            facts.append(fact)


    

# Print facts
# for fact in facts:
#     print(json.dumps(fact, indent=4))

# Write facts to a JSON file
with open('facts.json', 'w') as f:
    json.dump(facts, f, indent=4)






