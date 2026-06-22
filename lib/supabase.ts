import { createClient, type SupabaseClient } from "@supabase/supabase-js";

export function createSkyMotionSupabaseClient(): SupabaseClient | null {
  const supabaseUrl = process.env.NEXT_PUBLIC_SUPABASE_URL;
  const supabaseAnonKey = process.env.NEXT_PUBLIC_SUPABASE_ANON_KEY;

  if (!supabaseUrl || !supabaseAnonKey) {
    return null;
  }

  return createClient(supabaseUrl, supabaseAnonKey, {
    auth: {
      persistSession: true,
      autoRefreshToken: true,
    },
  });
}

export const storageBuckets = {
  uploads: "skymotion-uploads",
  renders: "skymotion-renders",
  avatars: "creator-avatars",
} as const;
