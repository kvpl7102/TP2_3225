# ConceptNet Knowledge Graph Explorer

## Overview

A single-page web application that provides an interactive interface for exploring the ConceptNet knowledge graph. The application features intelligent games, real-time concept exploration, and a comprehensive REST API for knowledge management. Built with JavaScript and Sammy.js, it integrates seamlessly with ConceptNet's API while maintaining a local MySQL database for enhanced performance and offline capabilities.

## Features

### **Knowledge Graph Exploration**
- **Real-time ConceptNet Integration**: Live AJAX queries to ConceptNet API for up-to-date information
- **Concept Browsing**: Explore relationships and connections between concepts
- **Multi-language Support**: Handle concepts in both French and English
- **Pagination System**: Efficient browsing of large datasets with integrated pagination

### **Interactive Learning Games**

#### "Qui Suis-Je?" (Who Am I?) Game
- **Timed Challenges**: Configurable time limits (default: 60 seconds)
- **Progressive Hints**: Clues revealed at customizable intervals (default: 10 seconds)
- **Scoring System**: `score = ⌈time/interval⌉ - hints_shown`
- **Dynamic Difficulty**: Concepts randomly selected from database

#### "Related Concepts" Game
- **Word Association**: Find concepts related to a given term
- **Real-time Validation**: Live checking against ConceptNet database
- **Performance Scoring**: Points awarded for correctly identified relationships
- **Time-based Challenges**: Configurable duration (default: 60 seconds)

### **Database Management**
- **MySQL Integration**: Persistent storage of concepts and relationships
- **Incremental Learning**: Database grows with user interactions
- **Statistics Tracking**: Monitor concepts, relations, facts, and users
- **User Session Management**: Secure login/logout functionality

### **REST API**
- **Concept Management**: CRUD operations for concepts and relationships
- **User Administration**: User creation and management endpoints
- **Statistics Endpoints**: Access to database metrics and analytics
- **Documentation**: Built-in API documentation via `/help` endpoint

## Technical Architecture

### Frontend Stack
- **Sammy.js**: Single-page application routing and navigation
- **jQuery**: DOM manipulation and AJAX operations
- **Bootstrap**: Responsive design and UI components
- **DataTables.net**: Advanced table pagination and sorting

### Backend Infrastructure
- **MySQL Database**: Persistent data storage
- **ConceptNet API**: External knowledge graph integration
- **RESTful Services**: API layer for data access
- **Session Management**: User authentication and state management

### Database Schema
```sql
-- Core entities
Concepts (id, concept, language)
Relations (id, relation_type, description)
Facts (id, start_concept, relation, end_concept)
Users (id, login, password_hash, score)
Sessions (id, user_id, session_token, created_at)
```

## Application Routes

### Authentication & Navigation
- `#/help` - Display implemented routes and documentation
- `#/login` - User authentication interface
- `#/logout` - Session termination
- `#/stats` - Database statistics dashboard
- `#/dump/faits` - Paginated facts table display

### ConceptNet Integration
- `#/concept/:language/:concept` - Explore specific concept relationships
- `#/relation/:relation/from/:language/:concept` - Query relations from specific concept
- `#/relation/:relation` - Browse all instances of a relation type

### Interactive Games
- `#/jeux/quisuisje/:time/:interval` - "Who Am I?" guessing game
- `#/jeux/related/:time` - Related concepts association game

### REST API Endpoints
- `GET /api/concepts` - List all concepts in database
- `GET /api/relations` - List all relation types
- `GET /api/users` - User management (login and scores)
- `POST /api/users` - Create new user account
- `GET /api/help` - API documentation

## Installation & Setup

### Database Setup
1. **Run Seed Script**
   ```bash
   ./seed.sh  # Generates initial dataset from ConceptNet
   ```

2. **Create Database**
   ```bash
   ./create-db.sh  # Initializes MySQL database with seed data
   ```

### Application Deployment
1. **Clone Repository**
   ```bash
   git clone [repository-url]
   cd conceptnet-explorer
   ```

2. **Configure Database Connection**
   ```javascript
   // Update config.js with your MySQL credentials
   const dbConfig = {
       host: 'localhost',
       user: 'your_username',
       password: 'your_password',
       database: 'conceptnet_db'
   };
   ```

3. **Deploy to Web Server**
   ```bash
   # Copy files to your web server directory
   cp -r * /var/www/html/conceptnet-explorer/
   ```

## Game Mechanics

### Qui Suis-Je? Example
```
Concept to guess: /c/en/dentist

Hints displayed progressively:
1. "? ? ? CapableOf cause you to have great pain"
2. "a receptionist AtLocation ? ? ?"
3. "? ? ? ReceivesAction paid to interact with you"
4. "old magazines AtLocation ? ? ?"
5. "? ? ? UsedFor clean your teeth"
```

### Related Concepts Example
```
Given concept: /c/en/kitchen
User inputs: "cooking, food, stove, refrigerator, dining"
Valid matches: cooking, food, stove, refrigerator
Score: 4 points
```

## Data Processing Pipeline

### ConceptNet Integration
1. **API Querying**: Real-time requests to ConceptNet endpoints
2. **Response Parsing**: JSON analysis and data extraction
3. **Database Storage**: Persistent caching of retrieved facts
4. **Pagination Handling**: Efficient large dataset management

### Example ConceptNet Fact
```json
{
  "start": "/c/fr/cité universitaire",
  "relation": "RelatedTo",
  "end": "/c/fr/étudiant",
  "weight": 2.5
}
```

## Performance Optimizations

- **AJAX Caching**: Reduce redundant API calls
- **Database Indexing**: Optimized queries for fast retrieval
- **Lazy Loading**: On-demand content loading
- **Pagination**: Efficient large dataset handling
- **Session Management**: Reduced authentication overhead

## Security Features

- **User Authentication**: Secure login/logout system
- **Session Management**: Token-based authentication
- **SQL Injection Prevention**: Parameterized queries
- **Input Validation**: Client and server-side validation

## Development Tools

### Testing & Debugging
- **Browser DevTools**: JavaScript debugging and network analysis
- **MySQL Workbench**: Database administration and query testing
- **Postman/curl**: REST API endpoint testing

### Code Quality
- **ESLint**: JavaScript code linting
- **JSDoc**: Comprehensive code documentation
- **Git Version Control**: Collaborative development workflow

## Key Learning Outcomes

- **Single-Page Applications**: Sammy.js routing and state management
- **AJAX Integration**: Asynchronous API communication
- **Database Design**: Relational data modeling and optimization
- **REST API Development**: RESTful service architecture
- **Knowledge Graphs**: Understanding of semantic relationships
- **Game Development**: Interactive learning application design

## Performance Metrics

- **Response Time**: < 200ms for database queries
- **API Integration**: < 500ms for ConceptNet requests
- **Game Responsiveness**: Real-time hint generation
- **Concurrent Users**: Supports multiple simultaneous sessions
- **Data Growth**: Incremental knowledge base expansion

---

*Built with JavaScript • Sammy.js • MySQL • ConceptNet API • Bootstrap • jQuery*
