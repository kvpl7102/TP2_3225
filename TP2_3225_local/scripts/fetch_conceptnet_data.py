import requests
import json

def fetch_conceptnet_data(start, rel, end, lang='en'):
    url = f"http://api.conceptnet.io/query?node=/c/{lang}/{start}&rel=/r/{rel}&other=/c/{lang}/{end}"
    response = requests.get(url)
    data = response.json()
    return data

# List of concepts and relationships
concepts = ['concept1', 'concept2', 'concept3', 'concept4', 'concept5', 'concept6', 'concept7', 'concept8', 'concept9', 'concept10',
            'concept11', 'concept12', 'concept13', 'concept14', 'concept15', 'concept16', 'concept17', 'concept18', 'concept19', 'concept20',
            'concept21', 'concept22', 'concept23', 'concept24', 'concept25', 'concept26', 'concept27', 'concept28', 'concept29', 'concept30',
            'concept31', 'concept32', 'concept33', 'concept34', 'concept35', 'concept36', 'concept37', 'concept38', 'concept39', 'concept40']
relationships = ['rel1', 'rel2', 'rel3', 'rel4', 'rel5', 'rel6', 'rel7', 'rel8', 'rel9', 'rel10']

facts = []

# Generate facts
for i in range(100):
    start = concepts[i % len(concepts)]
    rel = relationships[i % len(relationships)]
    end = concepts[(i+1) % len(concepts)]
    data = fetch_conceptnet_data(start, rel, end)
    facts.append(data)

# Print facts
for fact in facts:
    print(json.dumps(fact, indent=4))

with open('facts.json', 'w') as f:
    json.dump(facts, f)