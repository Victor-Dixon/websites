"use client";

import { DreamMotionStage } from "@/components/DreamMotionStage";

export function RenderPreviewPanel({
  videoUrl,
  statusLabel,
  progress,
  promptLabel,
}: {
  videoUrl?: string | null;
  statusLabel: string;
  progress?: number;
  promptLabel?: string;
}) {
  const showVideo = Boolean(videoUrl);

  return (
    <div className="relative h-80 overflow-hidden rounded-[1.25rem] bg-[radial-gradient(circle_at_50%_20%,rgba(125,211,252,0.6),transparent_22%),linear-gradient(150deg,#0f172a,#111827_48%,#2e1065)]">
      {showVideo ? (
        <video
          key={videoUrl}
          src={videoUrl ?? undefined}
          className="absolute inset-0 h-full w-full object-cover"
          autoPlay
          loop
          muted
          playsInline
          controls
          preload="auto"
        />
      ) : (
        <>
          <DreamMotionStage />
          <div className="absolute left-10 top-14 h-28 w-28 rounded-full bg-amber-200/70 blur-xl" />
          <div className="absolute bottom-[-4rem] left-[-2rem] h-56 w-72 rounded-[50%] bg-cyan-200/20 blur-sm" />
          <div className="absolute bottom-[-5rem] right-[-3rem] h-64 w-80 rounded-[50%] bg-fuchsia-300/20 blur-sm" />
        </>
      )}

      <div className="timeline-pulse absolute bottom-6 left-6 right-6 h-2 origin-left rounded-full bg-gradient-to-r from-cyan-300 via-fuchsia-400 to-white" />

      <div className="absolute left-4 top-4 rounded-xl bg-slate-950/75 px-3 py-2 text-xs text-cyan-100 backdrop-blur">
        {showVideo ? "Live render preview" : "Studio idle preview"}
      </div>

      <div className="absolute bottom-10 left-6 max-w-[85%] rounded-xl bg-slate-950/75 px-3 py-2 text-xs text-cyan-100 backdrop-blur">
        <div className="font-bold">{statusLabel}</div>
        {typeof progress === "number" && progress > 0 ? (
          <div className="mt-1 text-slate-300">{progress}% complete</div>
        ) : null}
        {promptLabel ? <div className="mt-1 line-clamp-2 text-slate-400">{promptLabel}</div> : null}
      </div>
    </div>
  );
}
