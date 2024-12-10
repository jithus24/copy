#include <stdio.h>
#include <stdlib.h>
#include <stdbool.h>

#define MAX 100

typedef struct Node {
    int vertex;
    struct Node* next;
} Node;

typedef struct Graph {
    int numVertices;
    Node* adjList[MAX];
    Node* revAdjList[MAX];
    bool visited[MAX];
} Graph;

// Function to create a new node
Node* createNode(int vertex) {
    Node* newNode = (Node*)malloc(sizeof(Node));
    newNode->vertex = vertex;
    newNode->next = NULL;
    return newNode;
}

// Function to create a graph
Graph* createGraph(int vertices) {
    Graph* graph = (Graph*)malloc(sizeof(Graph));
    graph->numVertices = vertices;

    for (int i = 0; i < vertices; i++) {
        graph->adjList[i] = NULL;
        graph->revAdjList[i] = NULL;
        graph->visited[i] = false;
    }

    return graph;
}

// Function to add an edge
void addEdge(Graph* graph, int src, int dest) {
    // Add edge to adjacency list
    Node* newNode = createNode(dest);
    newNode->next = graph->adjList[src];
    graph->adjList[src] = newNode;

    // Add edge to reverse adjacency list
    newNode = createNode(src);
    newNode->next = graph->revAdjList[dest];
    graph->revAdjList[dest] = newNode;
}

// Function to perform DFS and fill stack in order of finishing times
void dfsFillOrder(Graph* graph, int vertex, int* stack, int* top) {
    graph->visited[vertex] = true;

    Node* temp = graph->adjList[vertex];
    while (temp) {
        if (!graph->visited[temp->vertex]) {
            dfsFillOrder(graph, temp->vertex, stack, top);
        }
        temp = temp->next;
    }

    stack[(*top)++] = vertex;  // Push to stack
}

// Function to perform DFS on the reversed graph
void dfsOnReverseGraph(Graph* graph, int vertex) {
    graph->visited[vertex] = true;
    printf("%d ", vertex);

    Node* temp = graph->revAdjList[vertex];
    while (temp) {
        if (!graph->visited[temp->vertex]) {
            dfsOnReverseGraph(graph, temp->vertex);
        }
        temp = temp->next;
    }
}

// Function to find SCCs using Kosaraju's Algorithm
void findSCCs(Graph* graph) {
    int stack[MAX], top = 0;

    // Step 1: Fill the stack in the order of finishing times
    for (int i = 0; i < graph->numVertices; i++) {
        if (!graph->visited[i]) {
            dfsFillOrder(graph, i, stack, &top);
        }
    }

    // Step 2: Reset visited array for second DFS
    for (int i = 0; i < graph->numVertices; i++) {
        graph->visited[i] = false;
    }

    // Step 3: Process vertices in order of stack and find SCCs
    printf("\nStrongly Connected Components:\n");
    while (top > 0) {
        int vertex = stack[--top];
        if (!graph->visited[vertex]) {
            dfsOnReverseGraph(graph, vertex);
            printf("\n");
        }
    }
}

// Main function
int main() {
    int vertices, edges, src, dest;

    printf("Enter the number of vertices: ");
    scanf("%d", &vertices);

    Graph* graph = createGraph(vertices);

    printf("Enter the number of edges: ");
    scanf("%d", &edges);

    printf("Enter the edges (source destination):\n");
    for (int i = 0; i < edges; i++) {
        scanf("%d %d", &src, &dest);
        addEdge(graph, src, dest);
    }

    findSCCs(graph);

    return 0;
}
