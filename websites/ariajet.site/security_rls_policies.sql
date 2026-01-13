-- SECURITY FIX: RLS Policies for ariajet.site
-- Generated from Vibe Code Security Cleanup Kit Audit
-- Prevents unauthorized data access and enforces proper ownership

-- Enable RLS on critical tables
ALTER TABLE IF EXISTS users ENABLE ROW LEVEL SECURITY;
ALTER TABLE IF EXISTS subscriptions ENABLE ROW LEVEL SECURITY;
ALTER TABLE IF EXISTS payments ENABLE ROW LEVEL SECURITY;
ALTER TABLE IF EXISTS projects ENABLE ROW LEVEL SECURITY;
ALTER TABLE IF EXISTS api_keys ENABLE ROW LEVEL SECURITY;

-- USERS TABLE POLICIES
-- Users can only see/modify their own records
CREATE POLICY "users_select_own" ON users
    FOR SELECT USING (auth.uid() = id);

CREATE POLICY "users_update_own" ON users
    FOR UPDATE USING (auth.uid() = id)
    WITH CHECK (auth.uid() = id);

-- SUBSCRIPTIONS TABLE POLICIES
-- Users can only access their own subscriptions
CREATE POLICY "subscriptions_select_own" ON subscriptions
    FOR SELECT USING (auth.uid() = user_id);

CREATE POLICY "subscriptions_insert_own" ON subscriptions
    FOR INSERT WITH CHECK (auth.uid() = user_id);

CREATE POLICY "subscriptions_update_own" ON subscriptions
    FOR UPDATE USING (auth.uid() = user_id)
    WITH CHECK (auth.uid() = user_id);

-- PAYMENTS TABLE POLICIES
-- Users can only access their own payment records
CREATE POLICY "payments_select_own" ON payments
    FOR SELECT USING (auth.uid() = user_id);

CREATE POLICY "payments_insert_own" ON payments
    FOR INSERT WITH CHECK (auth.uid() = user_id);

-- PROJECTS TABLE POLICIES
-- Users can only access their own projects
CREATE POLICY "projects_select_own" ON projects
    FOR SELECT USING (auth.uid() = owner_id);

CREATE POLICY "projects_insert_own" ON projects
    FOR INSERT WITH CHECK (auth.uid() = owner_id);

CREATE POLICY "projects_update_own" ON projects
    FOR UPDATE USING (auth.uid() = owner_id)
    WITH CHECK (auth.uid() = owner_id);

CREATE POLICY "projects_delete_own" ON projects
    FOR DELETE USING (auth.uid() = owner_id);

-- API KEYS TABLE POLICIES
-- Users can only access their own API keys
CREATE POLICY "api_keys_select_own" ON api_keys
    FOR SELECT USING (auth.uid() = user_id);

CREATE POLICY "api_keys_insert_own" ON api_keys
    FOR INSERT WITH CHECK (auth.uid() = user_id);

CREATE POLICY "api_keys_update_own" ON api_keys
    FOR UPDATE USING (auth.uid() = user_id)
    WITH CHECK (auth.uid() = user_id);

CREATE POLICY "api_keys_delete_own" ON api_keys
    FOR DELETE USING (auth.uid() = user_id);

-- ADMIN POLICIES (for admin users only)
-- Admins can access all records for support purposes
CREATE POLICY "admin_full_access" ON users
    FOR ALL USING (
        EXISTS (
            SELECT 1 FROM user_roles
            WHERE user_id = auth.uid()
            AND role = 'admin'
        )
    );

-- Note: This assumes you have a user_roles table or similar
-- Adjust based on your actual role management system

COMMIT;