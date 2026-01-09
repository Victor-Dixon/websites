import Hero from "@/components/hero/Hero";

export default function Page() {
  return (
    <main>
      <Hero />
      <section className="mx-auto max-w-6xl px-6 pb-24">
        <div className="rounded-3xl border border-white/10 bg-white/5 p-8 backdrop-blur">
          <h2 className="text-xl font-semibold text-zinc-50">Latest</h2>
          <p className="mt-2 text-sm text-zinc-300">
            Replace this block with your post feed, stats, and strategy modules.
          </p>
        </div>
      </section>
    </main>
  );
}
