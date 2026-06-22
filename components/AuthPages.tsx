"use client";

import Link from "next/link";
import { type FormEvent, useMemo, useState } from "react";
import { clearLocalAccount, type AccountPlan, writeLocalSession } from "@/lib/account";
import { createSkyMotionSupabaseClient } from "@/lib/supabase";

const planOptions: Array<{ value: AccountPlan; title: string; detail: string }> = [
  {
    value: "free",
    title: "Free BYO key",
    detail: "Use your own video/provider API key so SkyMotion does not carry generation costs.",
  },
  {
    value: "premium",
    title: "Premium managed",
    detail: "Use SkyMotion managed capacity with rate limits designed to keep the plan profitable.",
  },
];

export function LoginPage() {
  const supabase = useMemo(() => createSkyMotionSupabaseClient(), []);
  const [email, setEmail] = useState("");
  const [plan, setPlan] = useState<AccountPlan>("free");
  const [status, setStatus] = useState("Create an account or log in to access the SkyMotion dashboard.");
  const [isSubmitting, setIsSubmitting] = useState(false);

  async function submitLogin(event: FormEvent<HTMLFormElement>) {
    event.preventDefault();
    const cleanEmail = email.trim().toLowerCase();

    if (!cleanEmail.includes("@")) {
      setStatus("Enter a valid email address.");
      return;
    }

    setIsSubmitting(true);
    setStatus("Creating your secure SkyMotion session...");

    try {
      if (supabase) {
        const { error } = await supabase.auth.signInWithOtp({
          email: cleanEmail,
          options: {
            emailRedirectTo: `${window.location.origin}/dashboard/`,
            data: {
              plan,
            },
          },
        });

        if (error) {
          throw error;
        }

        setStatus(`Magic link sent to ${cleanEmail}. Open it to continue to your dashboard.`);
      } else {
        writeLocalSession({
          email: cleanEmail,
          plan,
          source: "local",
          signedInAt: new Date().toISOString(),
        });
        setStatus("Account session created. Redirecting to your dashboard...");
        window.setTimeout(() => {
          window.location.href = "/dashboard/";
        }, 450);
      }
    } catch (error) {
      setStatus(error instanceof Error ? error.message : "Unable to start login. Please try again.");
    } finally {
      setIsSubmitting(false);
    }
  }

  return (
    <main className="relative min-h-screen px-4 py-6 text-slate-100 sm:px-6 lg:px-8">
      <AuthNav />
      <section className="mx-auto grid max-w-6xl gap-6 py-12 lg:grid-cols-[0.9fr_1.1fr]">
        <div className="flex flex-col justify-center">
          <p className="text-sm font-black uppercase tracking-[0.28em] text-cyan-200">Account required</p>
          <h1 className="mt-4 text-5xl font-black tracking-tight text-white sm:text-6xl">
            Log in to create with SkyMotion AI.
          </h1>
          <p className="mt-5 max-w-xl text-lg leading-8 text-slate-300">
            Usage is gated behind accounts so free users can bring their own provider keys and premium users can use
            SkyMotion managed capacity with rate limits.
          </p>
          <div className="mt-6 grid gap-3 sm:grid-cols-2">
            <AccountPolicyCard title="Free accounts" detail="Add your own API key in the dashboard before queuing renders." />
            <AccountPolicyCard title="Premium accounts" detail="No API key required; managed usage is throttled per account." />
          </div>
        </div>

        <form onSubmit={submitLogin} noValidate className="glass-panel rounded-[2rem] p-6 sm:p-8">
          <p className="text-xs font-black uppercase tracking-[0.24em] text-cyan-200">Login / create account</p>
          <h2 className="mt-3 text-3xl font-black text-white">Choose how you want to generate.</h2>
          <label htmlFor="email" className="mt-6 block text-sm font-bold text-cyan-100">
            Email address
          </label>
          <input
            id="email"
            type="email"
            value={email}
            onChange={(event) => setEmail(event.target.value)}
            placeholder="creator@studio.com"
            className="mt-2 w-full rounded-2xl border border-cyan-300/20 bg-slate-950/70 p-4 text-white outline-none ring-cyan-300/40 transition placeholder:text-slate-500 focus:ring-4"
          />

          <div className="mt-5 grid gap-3">
            {planOptions.map((option) => (
              <button
                key={option.value}
                type="button"
                onClick={() => setPlan(option.value)}
                className={`rounded-3xl border p-4 text-left transition hover:-translate-y-1 ${
                  plan === option.value
                    ? "border-cyan-300 bg-cyan-300/15"
                    : "border-white/10 bg-white/5 hover:bg-white/10"
                }`}
              >
                <span className="block text-base font-black text-white">{option.title}</span>
                <span className="mt-1 block text-sm leading-6 text-slate-300">{option.detail}</span>
              </button>
            ))}
          </div>

          <button
            type="submit"
            disabled={isSubmitting}
            className="mt-6 flex w-full items-center justify-center gap-2 rounded-2xl bg-cyan-300 px-5 py-4 font-black text-slate-950 transition hover:-translate-y-1 hover:bg-cyan-200 disabled:cursor-wait disabled:opacity-70"
          >
            {isSubmitting && <span className="loading-spinner" aria-hidden="true" />}
            {isSubmitting ? "Creating session..." : "Continue to Dashboard"}
          </button>
          <p className="mt-4 rounded-2xl border border-white/10 bg-white/5 p-4 text-sm leading-6 text-slate-300">{status}</p>
        </form>
      </section>
    </main>
  );
}

export function LogoutPage() {
  const supabase = useMemo(() => createSkyMotionSupabaseClient(), []);
  const [status, setStatus] = useState("Ready to sign out of SkyMotion.");
  const [isSigningOut, setIsSigningOut] = useState(false);

  async function signOut() {
    setIsSigningOut(true);
    setStatus("Signing out...");
    try {
      if (supabase) {
        await supabase.auth.signOut();
      }
      clearLocalAccount();
      setStatus("Signed out. Redirecting to the homepage...");
      window.setTimeout(() => {
        window.location.href = "/";
      }, 450);
    } catch (error) {
      setStatus(error instanceof Error ? error.message : "Unable to sign out. Please try again.");
    } finally {
      setIsSigningOut(false);
    }
  }

  return (
    <main className="relative min-h-screen px-4 py-6 text-slate-100 sm:px-6 lg:px-8">
      <AuthNav />
      <section className="mx-auto flex min-h-[70vh] max-w-3xl items-center justify-center">
        <div className="glass-panel w-full rounded-[2rem] p-8 text-center">
          <p className="text-xs font-black uppercase tracking-[0.24em] text-cyan-200">Logout</p>
          <h1 className="mt-3 text-4xl font-black text-white">End this SkyMotion session?</h1>
          <p className="mt-4 leading-7 text-slate-300">
            This clears the local account session and any free-account provider key stored in this browser.
          </p>
          <div className="mt-6 flex flex-col justify-center gap-3 sm:flex-row">
            <button
              type="button"
              onClick={signOut}
              disabled={isSigningOut}
              className="rounded-2xl bg-cyan-300 px-6 py-3 font-black text-slate-950 transition hover:-translate-y-1 hover:bg-cyan-200 disabled:cursor-wait disabled:opacity-70"
            >
              {isSigningOut ? "Signing out..." : "Log Out"}
            </button>
            <Link
              href="/dashboard/"
              className="rounded-2xl border border-white/15 bg-white/5 px-6 py-3 font-black text-white transition hover:-translate-y-1 hover:bg-white/10"
            >
              Back to Dashboard
            </Link>
          </div>
          <p className="mt-5 rounded-2xl border border-white/10 bg-white/5 p-4 text-sm text-slate-300">{status}</p>
        </div>
      </section>
    </main>
  );
}

function AuthNav() {
  return (
    <nav className="mx-auto flex max-w-7xl items-center justify-between rounded-full border border-white/10 bg-slate-950/75 px-4 py-3 shadow-2xl shadow-cyan-950/30 backdrop-blur-xl">
      <Link href="/" className="flex items-center gap-3" aria-label="SkyMotion AI home">
        <span className="grid h-11 w-11 place-items-center rounded-2xl bg-cyan-300 text-lg font-black text-slate-950 shadow-lg shadow-cyan-400/30">
          SK
        </span>
        <span>
          <span className="block text-sm font-semibold uppercase tracking-[0.32em] text-cyan-200">SkyMotion</span>
          <span className="block text-xs text-slate-400">AI Movie Studio</span>
        </span>
      </Link>
      <div className="flex items-center gap-2">
        <Link href="/dashboard/" className="rounded-full px-4 py-2 text-sm font-bold text-slate-300 transition hover:bg-white/10 hover:text-white">
          Dashboard
        </Link>
        <Link href="/login/" className="rounded-full bg-white px-4 py-2 text-sm font-black text-slate-950 transition hover:bg-cyan-200">
          Login
        </Link>
      </div>
    </nav>
  );
}

function AccountPolicyCard({ title, detail }: { title: string; detail: string }) {
  return (
    <article className="glass-panel rounded-3xl p-5">
      <h2 className="text-xl font-black text-white">{title}</h2>
      <p className="mt-2 text-sm leading-6 text-slate-300">{detail}</p>
    </article>
  );
}
