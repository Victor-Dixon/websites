"use client";

import Link from "next/link";
import { motion, useReducedMotion } from "framer-motion";
import { cn } from "@/lib/utils";

const container = {
  hidden: { opacity: 0 },
  show: {
    opacity: 1,
    transition: { staggerChildren: 0.08, delayChildren: 0.12 },
  },
};

const item = {
  hidden: { opacity: 0, y: 14, filter: "blur(6px)" },
  show: { opacity: 1, y: 0, filter: "blur(0px)", transition: { duration: 0.6, ease: "easeOut" } },
};

export default function Hero() {
  const reduce = useReducedMotion();

  return (
    <section className="relative overflow-hidden">
      {/* Ambient gradient blobs */}
      <div className="pointer-events-none absolute inset-0">
        <motion.div
          className="absolute -top-24 -left-24 h-[420px] w-[420px] rounded-full bg-fuchsia-500/20 blur-3xl"
          animate={reduce ? undefined : { x: [0, 40, 0], y: [0, 24, 0] }}
          transition={{ duration: 12, repeat: Infinity, ease: "easeInOut" }}
        />
        <motion.div
          className="absolute -bottom-24 -right-24 h-[520px] w-[520px] rounded-full bg-cyan-400/15 blur-3xl"
          animate={reduce ? undefined : { x: [0, -30, 0], y: [0, -18, 0] }}
          transition={{ duration: 14, repeat: Infinity, ease: "easeInOut" }}
        />
        <div className="grain absolute inset-0" />
      </div>

      {/* Grid overlay */}
      <div
        className="pointer-events-none absolute inset-0 opacity-[0.20]"
        style={{
          backgroundImage:
            "linear-gradient(rgba(255,255,255,0.06) 1px, transparent 1px), linear-gradient(90deg, rgba(255,255,255,0.06) 1px, transparent 1px)",
          backgroundSize: "48px 48px",
          maskImage: "radial-gradient(ellipse at 50% 40%, black 55%, transparent 80%)",
        }}
      />

      <div className="relative mx-auto max-w-6xl px-6 pb-20 pt-20 sm:pt-28">
        <motion.div variants={container} initial="hidden" animate="show" className="grid gap-10 lg:grid-cols-12">
          <div className="lg:col-span-7">
            <motion.div variants={item} className="inline-flex items-center gap-2 rounded-full border border-white/10 bg-white/5 px-3 py-1 text-xs text-zinc-200">
              <span className="h-2 w-2 animate-pulse rounded-full bg-emerald-400" />
              New theme refresh — fast, clean, focused
            </motion.div>

            <motion.h1
              variants={item}
              className="mt-5 text-balance text-4xl font-semibold tracking-tight sm:text-6xl"
            >
              Trade clarity.
              <span className="block bg-gradient-to-r from-zinc-50 via-zinc-50 to-zinc-300 bg-clip-text text-transparent">
                Systems that don’t flinch.
              </span>
            </motion.h1>

            <motion.p variants={item} className="mt-5 max-w-xl text-pretty text-base leading-relaxed text-zinc-200 sm:text-lg">
              FreeRideInvestor is the operating system for disciplined execution: journal → review → iterate.
              Clean signals. Tight risk. No drift.
            </motion.p>

            <motion.div variants={item} className="mt-7 flex flex-col gap-3 sm:flex-row sm:items-center">
              <Link
                href="/blog"
                className={cn(
                  "inline-flex items-center justify-center rounded-xl px-5 py-3 text-sm font-medium",
                  "bg-white text-zinc-950 hover:bg-zinc-100 transition"
                )}
              >
                Read the playbook
              </Link>
              <Link
                href="/about"
                className={cn(
                  "inline-flex items-center justify-center rounded-xl px-5 py-3 text-sm font-medium",
                  "border border-white/15 bg-white/5 text-zinc-50 hover:bg-white/10 transition"
                )}
              >
                How it works
              </Link>

              <div className="mt-2 text-xs text-zinc-400 sm:mt-0 sm:ml-2">
                Next.js • Tailwind • Framer Motion
              </div>
            </motion.div>

            <motion.div variants={item} className="mt-10 flex gap-6 text-sm text-zinc-300">
              <div>
                <div className="text-zinc-50 font-semibold">TBOW</div>
                <div className="text-zinc-400">rules-first execution</div>
              </div>
              <div className="h-10 w-px bg-white/10" />
              <div>
                <div className="text-zinc-50 font-semibold">Journal</div>
                <div className="text-zinc-400">review loops that stick</div>
              </div>
              <div className="h-10 w-px bg-white/10" />
              <div>
                <div className="text-zinc-50 font-semibold">Signals</div>
                <div className="text-zinc-400">clean + readable</div>
              </div>
            </motion.div>
          </div>

          {/* Right column: floating cards */}
          <div className="lg:col-span-5">
            <div className="relative h-[360px] sm:h-[420px]">
              <motion.div
                className="absolute inset-0 rounded-3xl border border-white/10 bg-gradient-to-b from-white/10 to-white/5 backdrop-blur"
                initial={{ opacity: 0, scale: 0.98 }}
                animate={{ opacity: 1, scale: 1 }}
                transition={{ duration: 0.8, ease: "easeOut" }}
              />

              <FloatingCard
                className="left-4 top-6"
                title="Risk"
                value="Defined"
                sub="Stops + size locked"
                delay={0.1}
              />
              <FloatingCard
                className="right-4 top-20"
                title="Trend"
                value="Respect"
                sub="No fighting tape"
                delay={0.18}
              />
              <FloatingCard
                className="left-6 bottom-10"
                title="Review"
                value="Weekly"
                sub="Metrics → adjustments"
                delay={0.26}
              />

              <motion.div
                className="absolute bottom-6 right-6 rounded-2xl border border-white/10 bg-zinc-950/40 px-4 py-3 text-xs text-zinc-200"
                variants={item}
                initial="hidden"
                animate="show"
              >
                <div className="font-semibold text-zinc-50">Live-ready UI</div>
                <div className="text-zinc-400">hero motion + modern typography</div>
              </motion.div>
            </div>
          </div>
        </motion.div>

        {/* Scroll hint */}
        <motion.div
          className="mt-14 flex items-center gap-3 text-xs text-zinc-400"
          initial={{ opacity: 0, y: 8 }}
          animate={{ opacity: 1, y: 0 }}
          transition={{ delay: 0.6, duration: 0.6 }}
        >
          <span className="inline-block h-px w-10 bg-white/15" />
          Scroll for posts & signals
        </motion.div>
      </div>
    </section>
  );
}

function FloatingCard({
  title,
  value,
  sub,
  className,
  delay,
}: {
  title: string;
  value: string;
  sub: string;
  className: string;
  delay: number;
}) {
  const reduce = useReducedMotion();

  return (
    <motion.div
      className={cn(
        "absolute w-[220px] rounded-2xl border border-white/10 bg-white/5 px-4 py-3 backdrop-blur",
        "shadow-[0_20px_80px_rgba(0,0,0,0.35)]",
        className
      )}
      initial={{ opacity: 0, y: 16, scale: 0.98 }}
      animate={{
        opacity: 1,
        y: 0,
        scale: 1,
      }}
      transition={{ delay, duration: 0.7, ease: "easeOut" }}
    >
      <div className="text-xs text-zinc-400">{title}</div>
      <div className="mt-1 text-lg font-semibold text-zinc-50">{value}</div>
      <div className="mt-1 text-xs text-zinc-400">{sub}</div>

      <motion.div
        className="mt-3 h-1 w-full rounded-full bg-white/10 overflow-hidden"
        aria-hidden="true"
      >
        <motion.div
          className="h-full w-1/2 rounded-full bg-gradient-to-r from-emerald-400/70 to-cyan-400/70"
          animate={reduce ? undefined : { x: ["-30%", "130%"] }}
          transition={{ duration: 2.8, repeat: Infinity, ease: "linear" }}
        />
      </motion.div>
    </motion.div>
  );
}
