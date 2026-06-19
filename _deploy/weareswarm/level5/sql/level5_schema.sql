CREATE TABLE IF NOT EXISTS level5_categories (
  id INT AUTO_INCREMENT PRIMARY KEY,
  slug VARCHAR(80) NOT NULL UNIQUE,
  label VARCHAR(160) NOT NULL,
  score INT NOT NULL DEFAULT 0,
  status VARCHAR(40) NOT NULL DEFAULT 'active',
  sort_order INT NOT NULL DEFAULT 0,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS level5_missions (
  id INT AUTO_INCREMENT PRIMARY KEY,
  title VARCHAR(220) NOT NULL,
  category_slug VARCHAR(80) NOT NULL,
  reward_points INT NOT NULL DEFAULT 1,
  executor_type VARCHAR(40) NOT NULL DEFAULT 'kids',
  status VARCHAR(40) NOT NULL DEFAULT 'available',
  objective TEXT,
  proof_required TEXT,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS level5_completed_missions (
  id INT AUTO_INCREMENT PRIMARY KEY,
  mission_id INT NULL,
  title VARCHAR(220) NOT NULL,
  category_slug VARCHAR(80) NOT NULL,
  reward_points INT NOT NULL DEFAULT 0,
  proof_url TEXT,
  completed_by VARCHAR(120),
  completed_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS kids_planner_tasks (
  id INT AUTO_INCREMENT PRIMARY KEY,
  mission_id INT NULL,
  title VARCHAR(220) NOT NULL,
  prompt TEXT,
  status VARCHAR(40) NOT NULL DEFAULT 'available',
  claimed_by VARCHAR(120),
  proof_url TEXT,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS agent_planner_tasks (
  id INT AUTO_INCREMENT PRIMARY KEY,
  mission_id INT NULL,
  title VARCHAR(220) NOT NULL,
  prompt TEXT,
  status VARCHAR(40) NOT NULL DEFAULT 'available',
  assigned_agent VARCHAR(120),
  proof_url TEXT,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS proof_receipts (
  id INT AUTO_INCREMENT PRIMARY KEY,
  source_type VARCHAR(80) NOT NULL,
  source_id INT NULL,
  title VARCHAR(220) NOT NULL,
  proof_url TEXT,
  proof_text TEXT,
  created_by VARCHAR(120),
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS score_events (
  id INT AUTO_INCREMENT PRIMARY KEY,
  category_slug VARCHAR(80) NOT NULL,
  delta INT NOT NULL,
  reason VARCHAR(220) NOT NULL,
  source_type VARCHAR(80),
  source_id INT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

INSERT IGNORE INTO level5_categories (slug,label,score,status,sort_order) VALUES
('automatic_routing','Automatic Routing',60,'active',10),
('task_claiming','Task Claiming',20,'active',20),
('proof_completion','Proof Completion',85,'active',30),
('multi_operator','Multi Operator',40,'active',40),
('revenue_engine','Revenue Engine',10,'active',50),
('kids_planner','Kids Planner',35,'active',60),
('agent_planner','Agent Planner',30,'active',70);

INSERT INTO level5_missions (title,category_slug,reward_points,executor_type,status,objective,proof_required) VALUES
('Website Audit Mission','revenue_engine',2,'kids','available','Audit one local business website and list three improvements.','Screenshot plus short report.'),
('Claim Workflow Prototype','task_claiming',3,'agent','available','Prototype a simple task claim workflow.','HTML/mockup or code artifact.'),
('Kids Prompt Generator','kids_planner',2,'kids','available','Create a copy-paste agent prompt from one task.','Generated prompt artifact.'),
('Agent Completion Receipt','agent_planner',2,'agent','available','Create a completion receipt template for agent work.','Receipt artifact.'),
('Routing Validation Report','automatic_routing',1,'agent','available','Check routing flags and produce validation report.','Report artifact.');
