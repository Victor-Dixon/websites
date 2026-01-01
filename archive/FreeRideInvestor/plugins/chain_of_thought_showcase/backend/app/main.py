
app = FastAPI(title="ChainOfThoughtReasoner API")

# Load environment variables
MODEL_NAME = get_unified_config().get_env("MODEL_NAME", "mistral:latest")
OLLAMA_URL = get_unified_config().get_env("OLLAMA_URL", "http://ollama:11434")
BACKEND_PORT = int(get_unified_config().get_env("BACKEND_PORT", 8000))

# Mock agent_dispatcher for demonstration purposes
class MockAgentDispatcher:
    async def dispatch_task(self, task: str, agent_name: str, use_chain_of_thought: bool):
        # Simulate processing time and response
        await asyncio.sleep(1)
        return f"Result for '{task}' by {agent_name}"

# Initialize the reasoner
agent_dispatcher = MockAgentDispatcher()
reasoner = ChainOfThoughtReasoner(agent_dispatcher=agent_dispatcher, model_name=MODEL_NAME, ollama_url=OLLAMA_URL)

class TaskRequest(BaseModel):
    task: str
    agent_name: str = "default_agent"

class TaskResponse(BaseModel):
    final_result: str
    reasoning_graph: dict

@app.post("/solve", response_model=TaskResponse)
async def solve_task(request: TaskRequest):
    final_result = await reasoner.solve_task_with_reasoning(request.task, request.agent_name)
    reasoning_graph = nx.node_link_data(reasoner.reasoning_graph)  # Serialize the graph
    return TaskResponse(final_result=final_result, reasoning_graph=reasoning_graph)

# Health check endpoint
@app.get("/health", tags=["Health Check"])
async def health_check():
    return {"status": "OK"}

# Root endpoint
@app.get("/", tags=["Root"])
async def read_root():
    return {"message": "Welcome to the ChainOfThoughtReasoner API"}

if __name__ == "__main__":
    uvicorn.run("main:app", host="0.0.0.0", port=BACKEND_PORT, reload=True)
