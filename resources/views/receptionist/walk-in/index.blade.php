@extends('layouts.receptionist.master')
@section('page_title', 'Đăng ký khách — Lễ tân DomusHub')
@push('styles')
<style>
/* =====================================================
   WALK-IN REGISTRATION — MODERN PREMIUM STYLES
   ===================================================== */

:root {
    --wi-primary: #3b82f6;
    --wi-primary-hover: #2563eb;
    --wi-primary-light: #eff6ff;
    --wi-success: #10b981;
    --wi-success-dark: #059669;
    --wi-success-light: #ecfdf5;
    --wi-text-main: #0f172a;
    --wi-text-muted: #64748b;
    --wi-bg-card: #ffffff;
    --wi-border-color: #e2e8f0;
    --wi-shadow-sm: 0 4px 6px -1px rgba(0, 0, 0, 0.03), 0 2px 4px -1px rgba(0, 0, 0, 0.02);
    --wi-shadow-lg: 0 20px 30px -10px rgba(15, 23, 42, 0.07), 0 10px 15px -5px rgba(15, 23, 42, 0.04);
}

.wi-wrap {
    max-width: 820px;
    margin: 0 auto;
    padding: 1.5rem 1rem 4rem;
    font-family: inherit;
}

/* ---- PAGE HEADER TITLE ---- */
.wi-header {
    text-align: center;
    margin-bottom: 2rem;
}
.wi-header-badge {
    display: inline-flex;
    align-items: center;
    gap: 0.4rem;
    padding: 0.35rem 0.85rem;
    border-radius: 9999px;
    background: linear-gradient(135deg, #e0e7ff, #dbeafe);
    color: #4338ca;
    font-size: 0.75rem;
    font-weight: 700;
    letter-spacing: 0.03em;
    margin-bottom: 0.6rem;
    text-transform: uppercase;
}
.wi-header-title {
    font-size: 1.65rem;
    font-weight: 800;
    color: var(--wi-text-main);
    letter-spacing: -0.02em;
    margin: 0 0 0.4rem;
}
.wi-header-sub {
    font-size: 0.88rem;
    color: var(--wi-text-muted);
    margin: 0;
}

/* ---- STEPPER WIZARD ---- */
.wi-stepper-card {
    background: var(--wi-bg-card);
    border: 1px solid var(--wi-border-color);
    border-radius: 20px;
    padding: 1.25rem 1.75rem;
    margin-bottom: 1.75rem;
    box-shadow: var(--wi-shadow-sm);
}
.wi-stepper {
    display: flex;
    align-items: center;
    justify-content: space-between;
    position: relative;
}
.wi-stepper-track {
    position: absolute;
    top: 20px;
    left: 40px;
    right: 40px;
    height: 3px;
    background: #e2e8f0;
    z-index: 0;
    border-radius: 99px;
    overflow: hidden;
}
.wi-stepper-progress {
    height: 100%;
    width: 0%;
    background: linear-gradient(90deg, #3b82f6, #6366f1, #10b981);
    transition: width 0.4s cubic-bezier(0.4, 0, 0.2, 1);
}
.wi-step-item {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 0.45rem;
    position: relative;
    z-index: 1;
    cursor: pointer;
    user-select: none;
}
.wi-step-circle {
    width: 42px;
    height: 42px;
    border-radius: 50%;
    border: 2px solid #cbd5e1;
    background: #ffffff;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 0.9rem;
    font-weight: 800;
    color: #64748b;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);
    transition: all 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
}
.wi-step-item.s-active .wi-step-circle {
    border-color: #3b82f6;
    background: #3b82f6;
    color: #ffffff;
    box-shadow: 0 0 0 5px rgba(59, 130, 246, 0.2);
    transform: scale(1.08);
}
.wi-step-item.s-done .wi-step-circle {
    border-color: #10b981;
    background: #10b981;
    color: #ffffff;
    box-shadow: 0 0 0 4px rgba(16, 185, 129, 0.15);
}
.wi-step-lbl {
    font-size: 0.78rem;
    font-weight: 700;
    color: #94a3b8;
    transition: color 0.3s;
}
.wi-step-item.s-active .wi-step-lbl {
    color: #2563eb;
    font-weight: 800;
}
.wi-step-item.s-done .wi-step-lbl {
    color: #059669;
}

/* ---- SECTION CARD ---- */
.wi-sec {
    background: var(--wi-bg-card);
    border: 1px solid var(--wi-border-color);
    border-radius: 20px;
    margin-bottom: 1.5rem;
    overflow: hidden;
    box-shadow: var(--wi-shadow-lg);
    transition: opacity 0.3s, transform 0.3s, box-shadow 0.3s;
}
.wi-sec.locked {
    opacity: 0.5;
    pointer-events: none;
    filter: grayscale(20%);
}
.wi-sec.hidden-step {
    display: none;
}
.wi-sec-hd {
    padding: 1.1rem 1.5rem;
    background: linear-gradient(to right, #f8fafc, #ffffff);
    border-bottom: 1px solid #edf2f7;
    display: flex;
    align-items: center;
    gap: 0.85rem;
}
.wi-sec-icon {
    width: 38px;
    height: 38px;
    border-radius: 12px;
    background: #eff6ff;
    color: #2563eb;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
    font-size: 1.05rem;
    box-shadow: inset 0 0 0 1px rgba(37, 99, 235, 0.1);
}
.wi-sec-title {
    font-size: 1rem;
    font-weight: 800;
    color: #0f172a;
    margin: 0;
    flex: 1;
}
.wi-sec-badge {
    font-size: 0.72rem;
    font-weight: 700;
    padding: 0.25rem 0.65rem;
    border-radius: 999px;
    background: #dcfce7;
    color: #15803d;
    display: none;
    align-items: center;
    gap: 0.3rem;
    border: 1px solid #bbf7d0;
}
.wi-sec-badge.show { display: inline-flex; }
.wi-sec-body { padding: 1.5rem; }

/* ---- FORM INPUTS ---- */
.wi-grid2 { display: grid; grid-template-columns: 1fr 1fr; gap: 1.1rem; }
@media (max-width: 600px) { .wi-grid2 { grid-template-columns: 1fr; } }
.wi-span2 { grid-column: 1 / -1; }
.wi-fld { display: flex; flex-direction: column; gap: 0.4rem; }
.wi-lbl {
    font-size: 0.75rem;
    font-weight: 700;
    color: #475569;
    text-transform: uppercase;
    letter-spacing: 0.04em;
    display: flex;
    align-items: center;
    gap: 0.35rem;
}
.wi-lbl em { color: #ef4444; font-style: normal; }

.wi-inp-wrap {
    position: relative;
    display: flex;
    align-items: center;
}
.wi-inp-icon {
    position: absolute;
    left: 0.85rem;
    color: #94a3b8;
    font-size: 0.9rem;
    pointer-events: none;
    transition: color 0.2s;
}
.wi-inp, .wi-sel, .wi-ta {
    background: #f8fafc;
    border: 1.5px solid #e2e8f0;
    border-radius: 12px;
    padding: 0.72rem 0.95rem 0.72rem 2.4rem;
    font-size: 0.9rem;
    font-family: inherit;
    color: #0f172a;
    width: 100%;
    box-sizing: border-box;
    transition: all 0.2s ease;
}
.wi-ta {
    padding-left: 0.95rem;
    min-height: 85px;
    resize: vertical;
    line-height: 1.5;
}
.wi-inp:focus, .wi-sel:focus, .wi-ta:focus {
    outline: none;
    border-color: #3b82f6;
    background: #ffffff;
    box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.12);
}
.wi-inp:focus + .wi-inp-icon, .wi-inp-wrap:focus-within .wi-inp-icon {
    color: #2563eb;
}
.wi-inp::placeholder, .wi-ta::placeholder { color: #94a3b8; }

/* QUICK REASON TAGS */
.wi-quick-tags {
    display: flex;
    flex-wrap: wrap;
    gap: 0.45rem;
    margin-top: 0.5rem;
}
.wi-tag-btn {
    padding: 0.3rem 0.7rem;
    background: #f1f5f9;
    border: 1px solid #cbd5e1;
    border-radius: 999px;
    font-size: 0.76rem;
    font-weight: 600;
    color: #475569;
    cursor: pointer;
    transition: all 0.15s;
    display: inline-flex;
    align-items: center;
    gap: 0.3rem;
}
.wi-tag-btn:hover {
    background: #eff6ff;
    border-color: #93c5fd;
    color: #1d4ed8;
    transform: translateY(-1px);
}
.wi-tag-btn:active { transform: translateY(0); }

/* APARTMENT DROPDOWN */
.wi-apt-wrap { position: relative; width: 100%; }
.wi-apt-dd {
    position: absolute;
    top: calc(100% + 6px);
    left: 0;
    right: 0;
    background: #ffffff;
    border: 1.5px solid #e2e8f0;
    border-radius: 14px;
    box-shadow: 0 12px 30px rgba(0, 0, 0, 0.12);
    max-height: 220px;
    overflow-y: auto;
    z-index: 300;
    display: none;
    padding: 0.4rem;
}
.wi-apt-dd.open { display: block; animation: fadeInDown 0.2s ease-out; }
@keyframes fadeInDown {
    from { opacity: 0; transform: translateY(-8px); }
    to { opacity: 1; transform: translateY(0); }
}
.wi-apt-opt {
    padding: 0.65rem 0.85rem;
    font-size: 0.88rem;
    font-weight: 600;
    cursor: pointer;
    color: #1e293b;
    border-radius: 8px;
    transition: background 0.15s, color 0.15s;
    display: flex;
    align-items: center;
    justify-content: space-between;
}
.wi-apt-opt:hover { background: #eff6ff; color: #1d4ed8; }
.wi-apt-opt--e { color: #94a3b8; cursor: default; justify-content: center; font-weight: 500; }

/* RESIDENT CHIPS / CARDS */
.wi-res-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
    gap: 0.75rem;
    margin-top: 0.4rem;
}
.wi-res {
    background: #ffffff;
    border: 1.5px solid #e2e8f0;
    border-radius: 14px;
    padding: 0.85rem;
    display: flex;
    align-items: center;
    gap: 0.75rem;
    cursor: pointer;
    transition: all 0.2s ease;
    box-shadow: var(--wi-shadow-sm);
    position: relative;
}
.wi-res:hover {
    border-color: #93c5fd;
    background: #f8fafc;
    transform: translateY(-2px);
    box-shadow: 0 6px 16px rgba(59, 130, 246, 0.1);
}
.wi-res.sel {
    background: linear-gradient(135deg, #f0fdf4, #dcfce7);
    border-color: #22c55e;
    box-shadow: 0 4px 14px rgba(34, 197, 94, 0.2);
}
.wi-res-avatar {
    width: 42px;
    height: 42px;
    border-radius: 50%;
    background: linear-gradient(135deg, #3b82f6, #6366f1);
    display: flex; align-items: center; justify-content: center;
    color: #ffffff; font-weight: 800; font-size: 1rem;
    flex-shrink: 0;
    box-shadow: 0 2px 6px rgba(59, 130, 246, 0.3);
}
.wi-res.sel .wi-res-avatar {
    background: linear-gradient(135deg, #10b981, #059669);
    box-shadow: 0 2px 6px rgba(16, 185, 129, 0.3);
}
.wi-res-info { flex: 1; min-width: 0; }
.wi-res-name { font-size: 0.9rem; font-weight: 800; color: #0f172a; margin-bottom: 0.15rem; truncate: true; }
.wi-res.sel .wi-res-name { color: #14532d; }
.wi-res-phone { font-size: 0.78rem; color: #64748b; font-weight: 600; display: flex; align-items: center; gap: 0.3rem; }
.wi-res.sel .wi-res-phone { color: #16a34a; }
.wi-res-chk {
    width: 22px; height: 22px;
    border-radius: 50%;
    background: #22c55e;
    color: #fff;
    display: none; align-items: center; justify-content: center;
    font-size: 0.7rem; flex-shrink: 0;
}
.wi-res.sel .wi-res-chk { display: flex; }

.wi-res-empty {
    background: #fffbe0;
    border: 1.5px dashed #fcd34d;
    border-radius: 14px;
    padding: 1rem 1.2rem;
    display: flex;
    align-items: center;
    gap: 0.75rem;
    color: #92400e;
    font-size: 0.88rem;
    font-weight: 600;
}

/* ---- CAMERA SECTION ---- */
.wi-cam-layout {
    display: grid;
    grid-template-columns: 1.2fr 1fr;
    gap: 1.25rem;
    align-items: start;
}
@media (max-width: 640px) { .wi-cam-layout { grid-template-columns: 1fr; } }

.wi-cam-container {
    background: #0f172a;
    border-radius: 16px;
    padding: 0.5rem;
    box-shadow: 0 8px 24px rgba(0, 0, 0, 0.2);
    position: relative;
    overflow: hidden;
}
.wi-cam-vp {
    width: 100%;
    aspect-ratio: 4 / 3;
    background: #1e293b;
    border-radius: 12px;
    overflow: hidden;
    display: flex;
    align-items: center;
    justify-content: center;
    position: relative;
}
.wi-cam-vp video { width: 100%; height: 100%; object-fit: cover; display: block; }
.wi-cam-overlay {
    position: absolute;
    inset: 0;
    pointer-events: none;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
}
.wi-cam-guide-oval {
    width: 55%;
    height: 70%;
    border: 2px dashed rgba(255, 255, 255, 0.45);
    border-radius: 50%;
    box-shadow: 0 0 0 9999px rgba(15, 23, 42, 0.35);
}
.wi-cam-live-badge {
    position: absolute;
    top: 12px;
    left: 12px;
    background: rgba(225, 29, 72, 0.85);
    backdrop-filter: blur(4px);
    color: #fff;
    font-size: 0.7rem;
    font-weight: 800;
    padding: 0.2rem 0.6rem;
    border-radius: 999px;
    display: flex;
    align-items: center;
    gap: 0.35rem;
    letter-spacing: 0.05em;
    z-index: 10;
}
.wi-cam-live-dot {
    width: 6px; height: 6px;
    border-radius: 50%;
    background: #fff;
    animation: blink 1.2s infinite;
}
@keyframes blink { 0%, 100% { opacity: 1; } 50% { opacity: 0.3; } }

.wi-cam-off {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 0.6rem;
    color: #94a3b8;
    text-align: center;
    padding: 1.5rem 1rem;
}
.wi-cam-off p { margin: 0; font-size: 0.85rem; font-weight: 600; color: #cbd5e1; }

.wi-snap-btn {
    display: flex; width: 100%; margin-top: 0.75rem;
    padding: 0.8rem 1.2rem;
    background: linear-gradient(135deg, #2563eb, #3b82f6);
    color: #ffffff;
    border: none; border-radius: 12px;
    font-size: 0.92rem; font-weight: 800; font-family: inherit;
    cursor: pointer; align-items: center; justify-content: center; gap: 0.6rem;
    box-shadow: 0 4px 14px rgba(37, 99, 235, 0.35); transition: all 0.2s;
}
.wi-snap-btn:hover { background: linear-gradient(135deg, #1d4ed8, #2563eb); transform: translateY(-1px); }
.wi-snap-btn:active { transform: translateY(0); }
.wi-snap-btn:disabled { opacity: 0.4; cursor: not-allowed; transform: none; box-shadow: none; }

.wi-prev-card {
    background: #f8fafc;
    border: 1.5px dashed #cbd5e1;
    border-radius: 16px;
    padding: 0.75rem;
    display: flex;
    flex-direction: column;
    align-items: center;
}
.wi-prev-lbl {
    font-size: 0.75rem; font-weight: 800;
    color: #475569; text-transform: uppercase; letter-spacing: 0.05em;
    margin: 0 0 0.6rem; align-self: flex-start;
}
.wi-prev-box {
    width: 100%; aspect-ratio: 4 / 3;
    background: #ffffff; border: 1px solid #e2e8f0;
    border-radius: 12px; overflow: hidden;
    display: flex; align-items: center; justify-content: center;
    position: relative;
    box-shadow: var(--wi-shadow-sm);
}
.wi-prev-box img { width: 100%; height: 100%; object-fit: cover; display: block; }
.wi-prev-empty {
    display: flex; flex-direction: column;
    align-items: center; gap: 0.5rem;
    color: #94a3b8; font-size: 0.78rem; text-align: center; padding: 1rem;
}
.wi-prev-empty i { font-size: 2rem; color: #cbd5e1; }
.wi-cam-acts {
    display: grid; grid-template-columns: 1fr 1fr;
    gap: 0.6rem; margin-top: 0.75rem; width: 100%;
}
.wi-btn-sm {
    padding: 0.6rem 0.8rem; border-radius: 10px;
    font-size: 0.82rem; font-weight: 700; font-family: inherit;
    cursor: pointer; border: 1.5px solid;
    display: flex; align-items: center; justify-content: center; gap: 0.4rem; transition: all 0.15s;
}
.wi-btn-outline { background: #fff; color: #475569; border-color: #cbd5e1; }
.wi-btn-outline:hover { background: #f1f5f9; color: #1e293b; }
.wi-btn-del { background: #fff; color: #dc2626; border-color: #fca5a5; }
.wi-btn-del:hover { background: #fef2f2; border-color: #f87171; }
.wi-btn-del:disabled, .wi-btn-sm:disabled { opacity: 0.45; cursor: not-allowed; }

/* ---- REVIEW STEP (DIGITAL VISITOR PASS MOCKUP) ---- */
.wi-pass-card {
    background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%);
    border: 1px solid #cbd5e1;
    border-radius: 18px;
    overflow: hidden;
    box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.05);
}
.wi-pass-hd {
    background: linear-gradient(135deg, #1e293b, #0f172a);
    color: #ffffff;
    padding: 0.85rem 1.25rem;
    display: flex;
    align-items: center;
    justify-content: space-between;
}
.wi-pass-title {
    font-size: 0.85rem;
    font-weight: 800;
    letter-spacing: 0.05em;
    text-transform: uppercase;
    display: flex;
    align-items: center;
    gap: 0.5rem;
    color: #f8fafc;
}
.wi-pass-body {
    padding: 1.25rem;
    display: grid;
    grid-template-columns: 1fr 110px;
    gap: 1.25rem;
    align-items: center;
}
@media (max-width: 520px) { .wi-pass-body { grid-template-columns: 1fr; } }
.wi-rv-rows { display: flex; flex-direction: column; gap: 0.55rem; }
.wi-rv-row { display: flex; align-items: baseline; gap: 0.6rem; font-size: 0.88rem; }
.wi-rv-lbl { color: #64748b; font-weight: 600; min-width: 120px; flex-shrink: 0; font-size: 0.82rem; }
.wi-rv-val { color: #0f172a; font-weight: 700; flex: 1; word-break: break-word; }
.wi-rv-val.accent { color: #2563eb; font-size: 1rem; }

.wi-rv-thumb {
    width: 110px; height: 110px;
    border-radius: 14px; overflow: hidden;
    border: 2px solid #3b82f6; background: #f1f5f9;
    display: flex; align-items: center; justify-content: center; flex-shrink: 0;
    box-shadow: 0 4px 12px rgba(59, 130, 246, 0.2);
    margin: 0 auto;
}
.wi-rv-thumb img { width: 100%; height: 100%; object-fit: cover; }
.wi-rv-thumb-empty {
    color: #94a3b8; font-size: 0.7rem;
    text-align: center; padding: 0.5rem;
    display: flex; flex-direction: column; align-items: center; gap: 0.3rem;
}

.wi-notice {
    background: #f0fdf4; border: 1.5px solid #bbf7d0;
    border-radius: 12px; padding: 0.85rem 1.1rem;
    display: flex; align-items: flex-start; gap: 0.75rem;
    font-size: 0.85rem; color: #166534; margin-top: 1rem;
    font-weight: 600;
}
.wi-notice i { font-size: 1.1rem; margin-top: 2px; color: #16a34a; }

/* ---- STEP ACTION BUTTONS ---- */
.wi-step-confirm {
    display: flex; align-items: center; justify-content: space-between;
    gap: 0.75rem; margin-top: 1.25rem; padding-top: 1.25rem;
    border-top: 1px solid #f1f5f9;
}
.wi-btn-confirm {
    padding: 0.75rem 1.75rem;
    background: linear-gradient(135deg, #2563eb, #3b82f6);
    color: #ffffff; border: none; border-radius: 12px;
    font-size: 0.92rem; font-weight: 800; font-family: inherit;
    cursor: pointer;
    display: inline-flex; align-items: center; gap: 0.5rem;
    box-shadow: 0 4px 14px rgba(37, 99, 235, 0.3);
    transition: all 0.2s;
    margin-left: auto;
}
.wi-btn-confirm:hover { background: linear-gradient(135deg, #1d4ed8, #2563eb); transform: translateY(-1px); }
.wi-btn-confirm:active { transform: translateY(0); }
.wi-btn-confirm:disabled { opacity: 0.5; cursor: not-allowed; transform: none; box-shadow: none; }
.wi-btn-edit {
    padding: 0.7rem 1.2rem;
    background: #ffffff; color: #475569;
    border: 1.5px solid #cbd5e1; border-radius: 11px;
    font-size: 0.85rem; font-weight: 700; font-family: inherit;
    cursor: pointer;
    display: inline-flex; align-items: center; gap: 0.45rem;
    transition: all 0.15s;
}
.wi-btn-edit:hover { background: #f8fafc; border-color: #94a3b8; color: #0f172a; }

/* ---- SUBMIT BUTTON ---- */
.wi-footer { margin-top: 1.75rem; }
.wi-btn-main {
    width: 100%;
    padding: 1.1rem 1rem;
    background: linear-gradient(135deg, #059669, #10b981);
    color: #ffffff; border: none; border-radius: 14px;
    font-size: 1.05rem; font-weight: 800; font-family: inherit;
    cursor: pointer;
    display: flex; align-items: center; justify-content: center; gap: 0.65rem;
    transition: all 0.2s;
    box-shadow: 0 6px 20px rgba(16, 185, 129, 0.35);
    letter-spacing: 0.02em;
}
.wi-btn-main:hover { background: linear-gradient(135deg, #047857, #059669); transform: translateY(-1px); }
.wi-btn-main:active { transform: translateY(0); }
.wi-btn-main:disabled { opacity: 0.55; cursor: not-allowed; transform: none; box-shadow: none; }
.wi-footer-links {
    display: flex; align-items: center; justify-content: center;
    gap: 1.75rem; margin-top: 1rem;
}
.wi-footer-link {
    font-size: 0.82rem; font-weight: 700; color: #64748b;
    text-decoration: none;
    display: flex; align-items: center; gap: 0.4rem;
    transition: color 0.15s;
}
.wi-footer-link:hover { color: #2563eb; }

/* UTILS */
.wi-spin {
    width: 20px; height: 20px;
    border: 2.5px solid rgba(255,255,255,.35);
    border-top-color: #fff; border-radius: 50%;
    animation: wspin .7s linear infinite; flex-shrink: 0;
}
.wi-spin-sm {
    width: 14px; height: 14px;
    border: 2px solid #e2e8f0;
    border-top-color: #3b82f6; border-radius: 50%;
    animation: wspin .7s linear infinite;
    display: inline-block; vertical-align: middle;
}
@keyframes wspin { to { transform: rotate(360deg); } }

.wi-toast {
    position: fixed; bottom: 2rem; right: 2rem;
    background: #0f172a; color: #ffffff; border-radius: 14px;
    padding: 0.95rem 1.25rem;
    display: flex; align-items: center; gap: 0.75rem;
    font-size: 0.88rem; font-weight: 600; z-index: 9999;
    transform: translateY(100px); opacity: 0;
    transition: all 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
    pointer-events: none;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.25);
    max-width: 400px;
    border: 1px solid rgba(255, 255, 255, 0.1);
}
.wi-toast.show { transform: translateY(0); opacity: 1; }
.wi-toast--success { background: #065f46; border-color: #10b981; }
.wi-toast--error   { background: #881337; border-color: #f43f5e; }
</style>
@endpush

@section('content')
<div class="wi-wrap">

    {{-- HEADER TITLE --}}
    <div class="wi-header">
        <div class="wi-header-badge">
            <i class="fa-solid fa-id-badge"></i> Lễ tân DomusHub
        </div>
        <h1 class="wi-header-title">Đăng Ký Khách Ghé Thăm Căn Hộ</h1>
        <p class="wi-header-sub">Quy trình ghi nhận khách ra vào và gửi thông báo trực tiếp tới cư dân</p>
    </div>

    {{-- STEPPER CARD --}}
    <div class="wi-stepper-card">
        <div class="wi-stepper">
            <div class="wi-stepper-track">
                <div class="wi-stepper-progress" id="stp-progress"></div>
            </div>
            <div class="wi-step-item s-active" id="stp-1" onclick="editStep(1)">
                <div class="wi-step-circle" id="stp-c-1">1</div>
                <span class="wi-step-lbl">Thông tin khách</span>
            </div>
            <div class="wi-step-item" id="stp-2" onclick="editStep(2)">
                <div class="wi-step-circle" id="stp-c-2">2</div>
                <span class="wi-step-lbl">Chụp ảnh định danh</span>
            </div>
            <div class="wi-step-item" id="stp-3">
                <div class="wi-step-circle" id="stp-c-3">3</div>
                <span class="wi-step-lbl">Xác nhận & Gửi</span>
            </div>
        </div>
    </div>

    {{-- ================================================
         PHẦN 1: THÔNG TIN KHÁCH
         ================================================ --}}
    <div class="wi-sec" id="sec-1">
        <div class="wi-sec-hd">
            <div class="wi-sec-icon">
                <i class="fa-solid fa-user-plus"></i>
            </div>
            <h2 class="wi-sec-title">Phần 1 — Thông tin khách & Căn hộ ghé thăm</h2>
            <span class="wi-sec-badge" id="badge-1">
                <i class="fa-solid fa-circle-check"></i> Đã hoàn tất
            </span>
        </div>
        <div class="wi-sec-body">
            <div class="wi-grid2">
                {{-- Họ tên khách --}}
                <div class="wi-fld">
                    <label class="wi-lbl" for="guest_name">Họ và tên khách <em>*</em></label>
                    <div class="wi-inp-wrap">
                        <input type="text" id="guest_name" class="wi-inp" placeholder="Ví dụ: Nguyễn Văn An" maxlength="100" autocomplete="off">
                        <i class="fa-solid fa-user wi-inp-icon"></i>
                    </div>
                </div>

                {{-- Số điện thoại --}}
                <div class="wi-fld">
                    <label class="wi-lbl" for="guest_phone">Số điện thoại <span style="font-size:.7rem;font-weight:500;color:#94a3b8;text-transform:none">(Ví dụ: 0912345678)</span></label>
                    <div class="wi-inp-wrap">
                        <input type="text" id="guest_phone" class="wi-inp" placeholder="090x xxx xxx" maxlength="10" pattern="^(03|05|07|08|09)[0-9]{8}$" autocomplete="off" inputmode="numeric">
                        <i class="fa-solid fa-phone wi-inp-icon"></i>
                    </div>
                </div>

                {{-- Chọn Căn hộ --}}
                <div class="wi-fld wi-span2">
                    <label class="wi-lbl">Căn hộ ghé thăm <em>*</em> <span id="apt-load" style="display:none"><span class="wi-spin-sm"></span></span></label>
                    <div class="wi-apt-wrap">
                        <div class="wi-inp-wrap">
                            <input type="text" id="apt_filter" class="wi-inp" placeholder="Gõ số phòng hoặc chọn từ danh sách..."
                                autocomplete="off" oninput="filterApt(this.value)" onfocus="openAptDd()" onblur="closeAptDd()">
                            <i class="fa-solid fa-building wi-inp-icon"></i>
                        </div>
                        <input type="hidden" id="apartment_id">
                        <div id="apt_dd" class="wi-apt-dd">
                            @foreach($apartments as $apt)
                            <div class="wi-apt-opt" onmousedown="pickApt({{ $apt->id }},'{{ addslashes($apt->apartment_number.($apt->floor?->block?' ('.$apt->floor->block->name.')':'')) }}')">
                                <span><i class="fa-solid fa-door-closed" style="color:#94a3b8;margin-right:6px"></i> {{ $apt->apartment_number }}</span>
                                @if($apt->floor?->block)
                                <span style="font-size:.75rem;color:#64748b;background:#f1f5f9;padding:2px 6px;border-radius:4px">{{ $apt->floor->block->name }}</span>
                                @endif
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                {{-- Chọn Cư dân --}}
                <div class="wi-fld wi-span2" id="res-box" style="display:none">
                    <label class="wi-lbl"><i class="fa-solid fa-users" style="color:#3b82f6"></i> Cư dân đón khách <em>*</em></label>
                    <div id="res-list"></div>
                    <input type="hidden" id="confirmed_by_resident">
                    <input type="text" id="res_manual" class="wi-inp" style="display:none;margin-top:.5rem;padding-left:1rem" placeholder="Nhập tên cư dân cần gặp..." maxlength="100">
                </div>

                {{-- Lý do ghé thăm --}}
                <div class="wi-fld wi-span2">
                    <label class="wi-lbl" for="note">Lý do ghé thăm <em>*</em></label>
                    <textarea id="note" class="wi-ta" placeholder="Nhập mục đích khách đến (thăm người thân, giao hàng, bảo trì...)..."></textarea>
                    
                    {{-- Quick tags --}}
                    <div class="wi-quick-tags">
                        <span style="font-size:.72rem;color:#94a3b8;align-self:center;margin-right:2px">Chọn nhanh:</span>
                        <button type="button" class="wi-tag-btn" onclick="setQuickNote('Thăm người thân')">Thăm người thân</button>
                        <button type="button" class="wi-tag-btn" onclick="setQuickNote('Giao hàng / Shipper')">Giao hàng</button>
                        <button type="button" class="wi-tag-btn" onclick="setQuickNote('Bảo trì / Sửa chữa')">Bảo trì</button>
                        <button type="button" class="wi-tag-btn" onclick="setQuickNote('Công việc / Họp')">Công việc</button>
                    </div>
                </div>
            </div>

            {{-- Confirm Step 1 --}}
            <div class="wi-step-confirm">
                <span id="s1-hint" style="font-size:.82rem;color:#94a3b8"><i class="fa-solid fa-circle-info"></i> Điền đầy đủ thông tin khách và chọn cư dân trước khi tiếp tục.</span>
                <button type="button" class="wi-btn-confirm" id="btn-confirm-1" onclick="confirmStep1()">
                    Tiếp tục chụp ảnh <i class="fa-solid fa-arrow-right"></i>
                </button>
            </div>
        </div>
    </div>

    {{-- ================================================
         PHẦN 2: CHỤP ẢNH ĐỊNH DANH
         ================================================ --}}
    <div class="wi-sec hidden-step" id="sec-2">
        <div class="wi-sec-hd">
            <div class="wi-sec-icon" style="background:#fef3c7;color:#d97706">
                <i class="fa-solid fa-camera"></i>
            </div>
            <h2 class="wi-sec-title">Phần 2 — Chụp ảnh định danh khách hàng</h2>
            <span class="wi-sec-badge" id="badge-2">
                <i class="fa-solid fa-circle-check"></i> Đã chụp ảnh
            </span>
        </div>
        <div class="wi-sec-body">
            <div class="wi-cam-layout">
                {{-- Live Camera Box --}}
                <div>
                    <div class="wi-cam-container">
                        <div class="wi-cam-vp" id="cam-vp">
                            <div class="wi-cam-overlay">
                                <div class="wi-cam-guide-oval"></div>
                            </div>
                            <video id="cam-video" autoplay playsinline muted style="display:none;width:100%;height:100%;object-fit:cover"></video>
                            <div class="wi-cam-off" id="cam-off">
                                <i class="fa-solid fa-video-slash" style="font-size:2.5rem;color:#475569"></i>
                                <p>Camera đang tắt</p>
                                <span style="font-size:.76rem;color:#64748b">Nhấn nút bên dưới để bật webcam</span>
                            </div>
                        </div>
                    </div>
                    <button type="button" id="btn-snap" class="wi-snap-btn" onclick="snapPhoto()" disabled>
                        <i class="fa-solid fa-camera"></i> CHỤP ẢNH NGAY <span style="font-size:.75rem;opacity:.8">(F9)</span>
                    </button>
                </div>

                {{-- Photo Preview Box --}}
                <div class="wi-prev-card">
                    <p class="wi-prev-lbl"><i class="fa-solid fa-image"></i> Ảnh xem trước</p>
                    <div class="wi-prev-box">
                        <div class="wi-prev-empty" id="prev-empty">
                            <i class="fa-solid fa-image-portrait"></i>
                            <p>Hình ảnh đã chụp<br>sẽ xuất hiện ở đây</p>
                        </div>
                        <img id="prev-img" src="" alt="Ảnh chụp khách" style="display:none;width:100%;height:100%;object-fit:cover">
                    </div>
                    <div class="wi-cam-acts">
                        <button type="button" class="wi-btn-sm wi-btn-outline" id="btn-cam" onclick="toggleCam()">
                            <i class="fa-solid fa-power-off"></i> <span id="cam-lbl">Bật camera</span>
                        </button>
                        <button type="button" class="wi-btn-sm wi-btn-del" id="btn-del" onclick="clearPhoto()" disabled>
                            <i class="fa-solid fa-trash-can"></i> Xoá & Chụp lại
                        </button>
                    </div>
                </div>
            </div>
            <canvas id="wi-canvas" style="display:none"></canvas>
            <input type="hidden" id="face_image_data">

            {{-- Confirm Step 2 --}}
            <div class="wi-step-confirm">
                <button type="button" class="wi-btn-edit" onclick="editStep(1)">
                    <i class="fa-solid fa-arrow-left"></i> Quay lại sửa thông tin
                </button>
                <button type="button" class="wi-btn-confirm" id="btn-confirm-2" onclick="confirmStep2()" disabled>
                    Xác nhận ảnh & Tiếp tục <i class="fa-solid fa-arrow-right"></i>
                </button>
            </div>
        </div>
    </div>

    {{-- ================================================
         PHẦN 3: KIỂM TRA & XÁC NHẬN GỬI
         ================================================ --}}
    <div class="wi-sec hidden-step" id="sec-3">
        <div class="wi-sec-hd">
            <div class="wi-sec-icon" style="background:#dcfce7;color:#15803d">
                <i class="fa-solid fa-paper-plane"></i>
            </div>
            <h2 class="wi-sec-title" style="color:#15803d">Phần 3 — Xác nhận thông tin & Gửi thông báo cư dân</h2>
        </div>
        <div class="wi-sec-body">

            {{-- Digital Visitor Pass Summary Card --}}
            <div class="wi-pass-card">
                <div class="wi-pass-hd">
                    <div class="wi-pass-title">
                        <i class="fa-solid fa-shield-halved"></i> DomusHub Visitor Security Pass
                    </div>
                    <span style="font-size:.72rem;background:rgba(255,255,255,.2);padding:2px 8px;border-radius:99px;font-weight:700">TẬN TÂM - AN TOÀN</span>
                </div>
                <div class="wi-pass-body">
                    <div class="wi-rv-rows">
                        <div class="wi-rv-row">
                            <span class="wi-rv-lbl"><i class="fa-solid fa-user"></i> Họ tên khách:</span>
                            <span class="wi-rv-val accent" id="rv-name">—</span>
                        </div>
                        <div class="wi-rv-row">
                            <span class="wi-rv-lbl"><i class="fa-solid fa-phone"></i> Số điện thoại:</span>
                            <span class="wi-rv-val" id="rv-phone">—</span>
                        </div>
                        <div class="wi-rv-row">
                            <span class="wi-rv-lbl"><i class="fa-solid fa-building"></i> Căn hộ ghé thăm:</span>
                            <span class="wi-rv-val" id="rv-apt">—</span>
                        </div>
                        <div class="wi-rv-row">
                            <span class="wi-rv-lbl"><i class="fa-solid fa-user-check"></i> Cư dân tiếp đón:</span>
                            <span class="wi-rv-val" id="rv-resident" style="color:#15803d;font-weight:800">—</span>
                        </div>
                        <div class="wi-rv-row" id="rv-note-row" style="display:none">
                            <span class="wi-rv-lbl"><i class="fa-solid fa-comment-dots"></i> Lý do thăm:</span>
                            <span class="wi-rv-val" id="rv-note" style="font-style:italic;color:#475569"></span>
                        </div>
                        <div class="wi-rv-row">
                            <span class="wi-rv-lbl"><i class="fa-solid fa-camera"></i> Ảnh định danh:</span>
                            <span class="wi-rv-val" id="rv-photo-status">—</span>
                        </div>
                    </div>

                    {{-- Pass Photo Thumbnail --}}
                    <div class="wi-rv-thumb">
                        <div class="wi-rv-thumb-empty" id="rv-thumb-empty">
                            <i class="fa-solid fa-user-shield" style="font-size:1.8rem;color:#cbd5e1"></i>
                            <span>Ảnh khách</span>
                        </div>
                        <img id="rv-photo" src="" alt="Ảnh định danh khách" style="display:none;width:100%;height:100%;object-fit:cover">
                    </div>
                </div>
            </div>

            {{-- Resident notify notice --}}
            <div class="wi-notice" id="rv-notice">
                <i class="fa-solid fa-bell-concierge"></i>
                <span id="rv-notice-text">Sẵn sàng gửi thông báo đến ứng dụng cư dân.</span>
            </div>

            {{-- Final Submit --}}
            <div class="wi-footer">
                <button type="button" id="btn-submit" class="wi-btn-main" onclick="doSubmit()">
                    <i class="fa-solid fa-paper-plane"></i> GỬI THÔNG BÁO CHO CƯ DÂN & GHI NHẬN
                </button>
                <div class="wi-footer-links">
                    <a href="{{ route('receptionist.visitor-log.index') }}" class="wi-footer-link">
                        <i class="fa-solid fa-clock-rotate-left"></i> Xem lịch sử ra vào
                    </a>
                    <a href="#" class="wi-footer-link" onclick="resetForm();return false" style="color:#ef4444">
                        <i class="fa-solid fa-rotate-left"></i> Huỷ & Đăng ký lại
                    </a>
                </div>
            </div>
        </div>
    </div>

</div>{{-- /wi-wrap --}}

<div class="wi-toast" id="wi-toast">
    <span id="toast-icon" style="font-size:1.1rem"></span>
    <span id="toast-msg"></span>
</div>

<script>
const CSRF      = document.querySelector('meta[name="csrf-token"]')?.content || '{{ csrf_token() }}';
const URL_RES   = '{{ route("receptionist.walk-in.residents") }}';
const URL_STORE = '{{ route("receptionist.walk-in.store") }}';
const APT_DATA  = [
    @foreach($apartments as $apt)
    {id:{{ $apt->id }},label:'{{ addslashes($apt->apartment_number.($apt->floor?->block?' ('.$apt->floor->block->name.')':'')) }}'},
    @endforeach
];

let selResId = '', selResName = '', camStream = null, camOn = false;
let currentStep = 1;

/* =====================================================
   QUICK REASON TAGS
   ===================================================== */
function setQuickNote(val) {
    const noteEl = document.getElementById('note');
    noteEl.value = val;
    noteEl.focus();
}

/* =====================================================
   STEPPER
   ===================================================== */
function setStep(n) {
    currentStep = n;
    
    // Update progress bar
    const progressEl = document.getElementById('stp-progress');
    if (progressEl) {
        progressEl.style.width = n === 1 ? '0%' : (n === 2 ? '50%' : '100%');
    }

    [1,2,3].forEach(i => {
        const it = document.getElementById('stp-' + i);
        const c  = document.getElementById('stp-c-' + i);
        if (!it || !c) return;
        
        it.className = 'wi-step-item' + (i < n ? ' s-done' : (i === n ? ' s-active' : ''));
        c.innerHTML = i < n
            ? '<i class="fa-solid fa-check"></i>'
            : i;
    });

    [1,2,3].forEach(i => {
        const sec = document.getElementById('sec-' + i);
        if (!sec) return;
        if (i === n) {
            sec.classList.remove('hidden-step', 'locked');
        } else if (i < n) {
            sec.classList.remove('hidden-step');
            sec.classList.add('locked');
        } else {
            sec.classList.add('hidden-step');
            sec.classList.remove('locked');
        }
    });

    window.scrollTo({ top: 120, behavior: 'smooth' });
}

function editStep(n) {
    if (n >= currentStep && n !== 1) return;
    setStep(n);
    if (n === 1) {
        document.getElementById('badge-1').classList.remove('show');
    }
    if (n === 2) {
        document.getElementById('badge-2').classList.remove('show');
    }
}

/* =====================================================
   STEP 1 CONFIRM
   ===================================================== */
function confirmStep1() {
    const name = document.getElementById('guest_name').value.trim();
    const phone = document.getElementById('guest_phone').value.trim();
    const aptId = document.getElementById('apartment_id').value;
    const resident = selResName || document.getElementById('res_manual').value.trim();

    if (!name) {
        showToast('Vui lòng nhập họ tên khách.', 'error');
        document.getElementById('guest_name').focus(); return;
    }
    if (phone && !/^(03|05|07|08|09)[0-9]{8}$/.test(phone)) {
        showToast('Số điện thoại phải gồm đúng 10 chữ số, bắt đầu bằng đầu số Việt Nam (03, 05, 07, 08, 09).', 'error');
        document.getElementById('guest_phone').focus(); return;
    }
    if (!aptId) {
        showToast('Vui lòng chọn căn hộ ghé thăm.', 'error');
        document.getElementById('apt_filter').focus(); return;
    }
    if (!resident) {
        showToast('Vui lòng chọn hoặc nhập tên cư dân đón khách.', 'error'); return;
    }
    const note = document.getElementById('note').value.trim();
    if (!note) {
        showToast('Vui lòng nhập hoặc chọn lý do ghé thăm.', 'error');
        document.getElementById('note').focus(); return;
    }

    document.getElementById('badge-1').classList.add('show');
    setStep(2);
    // Auto start camera if not started
    if (!camOn) {
        startCam();
    }
}

/* =====================================================
   STEP 2 CONFIRM
   ===================================================== */
function confirmStep2() {
    const imgData = document.getElementById('face_image_data').value;
    if (!imgData) {
        showToast('Vui lòng chụp ảnh định danh trước khi tiếp tục.', 'error'); return;
    }
    document.getElementById('badge-2').classList.add('show');
    stopCam();
    updateReview();
    setStep(3);
}

/* =====================================================
   APARTMENT DROPDOWN
   ===================================================== */
function openAptDd() { filterApt(document.getElementById('apt_filter').value); document.getElementById('apt_dd').classList.add('open'); }
function closeAptDd() { setTimeout(() => document.getElementById('apt_dd').classList.remove('open'), 180); }
function filterApt(q) {
    const dd = document.getElementById('apt_dd'), lo = q.trim().toLowerCase();
    const items = APT_DATA.filter(a => a.label.toLowerCase().includes(lo));
    dd.innerHTML = items.length === 0
        ? '<div class="wi-apt-opt wi-apt-opt--e"><i class="fa-solid fa-circle-exclamation"></i> Không tìm thấy căn hộ</div>'
        : items.map(a => `<div class="wi-apt-opt" onmousedown="pickApt(${a.id},'${a.label.replace(/'/g,"\\'")}')"><span><i class="fa-solid fa-door-closed" style="color:#94a3b8;margin-right:6px"></i> ${a.label}</span><i class="fa-solid fa-chevron-right" style="font-size:.7rem;color:#cbd5e1"></i></div>`).join('');
    dd.classList.add('open');
}
function pickApt(id, label) {
    document.getElementById('apt_filter').value = label;
    document.getElementById('apartment_id').value = id;
    document.getElementById('apt_dd').classList.remove('open');
    loadRes(id);
}
</script>

<script>
/* =====================================================
   RESIDENTS
   ===================================================== */
async function loadRes(aptId) {
    const box = document.getElementById('res-box'), list = document.getElementById('res-list');
    const manual = document.getElementById('res_manual'), loader = document.getElementById('apt-load');
    selResId = ''; selResName = '';
    document.getElementById('confirmed_by_resident').value = '';
    box.style.display = 'none'; manual.style.display = 'none'; manual.value = '';
    if (!aptId) return;
    loader.style.display = 'inline-block';
    try {
        const r = await fetch(URL_RES + '?apartment_id=' + aptId, { headers: { Accept: 'application/json', 'X-CSRF-TOKEN': CSRF } });
        if (!r.ok) throw new Error('HTTP ' + r.status);
        const data = await r.json(); const residents = data.residents || [];
        loader.style.display = 'none'; box.style.display = 'block';
        if (!residents.length) {
            list.innerHTML = '<div class="wi-res-empty"><i class="fa-solid fa-triangle-exclamation" style="font-size:1.2rem"></i> <div>Căn hộ chưa có cư dân đăng ký trên hệ thống. Vui lòng nhập thủ công bên dưới.</div></div>';
            manual.style.display = 'block'; manual.focus();
        } else {
            list.className = 'wi-res-grid';
            list.innerHTML = residents.map(r => `
                <div class="wi-res" id="res-${r.id}" onclick="pickRes(${r.id},'${(r.name||'').replace(/\\/g,"\\\\").replace(/'/g,"\\'")}')">
                    <div class="wi-res-avatar">${(r.name||'C').charAt(0).toUpperCase()}</div>
                    <div class="wi-res-info">
                        <div class="wi-res-name">${r.name||'Cư dân'}</div>
                        <div class="wi-res-phone">${r.phone?'<i class="fa-solid fa-phone"></i> '+r.phone:'<span style="color:#94a3b8">Chưa có SĐT</span>'}</div>
                    </div>
                    <div class="wi-res-chk"><i class="fa-solid fa-check"></i></div>
                </div>`).join('');
            if (residents.length === 1) pickRes(residents[0].id, residents[0].name);
        }
    } catch (err) {
        console.error('Lỗi tải cư dân:', err);
        loader.style.display = 'none'; box.style.display = 'block';
        list.innerHTML = '<p style="color:#ef4444;font-size:.85rem;font-weight:600"><i class="fa-solid fa-circle-exclamation"></i> Không thể tải danh sách cư dân.</p>';
    }
}
function pickRes(id, name) {
    document.querySelectorAll('.wi-res').forEach(el => el.classList.remove('sel'));
    document.getElementById('res-' + id)?.classList.add('sel');
    selResId = id; selResName = name;
    document.getElementById('confirmed_by_resident').value = id;
}

/* =====================================================
   CAMERA
   ===================================================== */
async function toggleCam() { camOn ? stopCam() : await startCam(); }
async function startCam() {
    try {
        camStream = await navigator.mediaDevices.getUserMedia({ video: { facingMode: 'environment' }, audio: false });
        const v = document.getElementById('cam-video');
        v.srcObject = camStream; v.style.display = 'block';
        document.getElementById('cam-off').style.display = 'none';
        document.getElementById('btn-snap').disabled = false;
        document.getElementById('cam-lbl').textContent = 'Tắt camera';
        camOn = true;
    } catch (e) { showToast('Không thể truy cập camera: ' + e.message, 'error'); }
}
function stopCam() {
    if (camStream) { camStream.getTracks().forEach(t => t.stop()); camStream = null; }
    const v = document.getElementById('cam-video');
    v.srcObject = null; v.style.display = 'none';
    document.getElementById('cam-off').style.display = 'flex';
    document.getElementById('btn-snap').disabled = true;
    document.getElementById('cam-lbl').textContent = 'Bật camera';
    camOn = false;
}
function snapPhoto() {
    const v = document.getElementById('cam-video'), c = document.getElementById('wi-canvas');
    c.width = v.videoWidth || 640; c.height = v.videoHeight || 480;
    c.getContext('2d').drawImage(v, 0, 0);
    const url = c.toDataURL('image/jpeg', .88);
    document.getElementById('face_image_data').value = url;
    document.getElementById('prev-img').src = url;
    document.getElementById('prev-img').style.display = 'block';
    document.getElementById('prev-empty').style.display = 'none';
    document.getElementById('btn-del').disabled = false;
    document.getElementById('btn-confirm-2').disabled = false;
    stopCam();
    showToast('Đã chụp ảnh định danh thành công! Kiểm tra và nhấn Tiếp tục.', 'success');
}
function clearPhoto() {
    document.getElementById('face_image_data').value = '';
    document.getElementById('prev-img').style.display = 'none';
    document.getElementById('prev-empty').style.display = 'flex';
    document.getElementById('btn-del').disabled = true;
    document.getElementById('btn-confirm-2').disabled = true;
    startCam();
}
document.addEventListener('keydown', e => {
    if (e.key === 'F9') { e.preventDefault(); if (!document.getElementById('btn-snap').disabled) snapPhoto(); }
});
</script>

<script>
/* =====================================================
   REVIEW (Step 3)
   ===================================================== */
function updateReview() {
    const name     = document.getElementById('guest_name').value.trim();
    const phone    = document.getElementById('guest_phone').value.trim();
    const aptLbl   = document.getElementById('apt_filter').value.trim();
    const manual   = document.getElementById('res_manual').value.trim();
    const resident = selResName || manual;
    const note     = document.getElementById('note').value.trim();
    const imgData  = document.getElementById('face_image_data').value;

    document.getElementById('rv-name').textContent     = name    || '—';
    document.getElementById('rv-phone').textContent    = phone   || 'Chưa cung cấp';
    document.getElementById('rv-apt').textContent      = aptLbl  || '—';
    document.getElementById('rv-resident').textContent = resident || '—';

    const nr = document.getElementById('rv-note-row');
    if (note) { document.getElementById('rv-note').textContent = '"' + note + '"'; nr.style.display = 'flex'; }
    else { nr.style.display = 'none'; }

    const photoStatus = document.getElementById('rv-photo-status');
    if (imgData) {
        document.getElementById('rv-photo').src = imgData;
        document.getElementById('rv-photo').style.display = 'block';
        document.getElementById('rv-thumb-empty').style.display = 'none';
        photoStatus.innerHTML = '<span style="color:#059669;font-weight:800">Đã có ảnh chụp định danh</span>';
    } else {
        document.getElementById('rv-photo').style.display = 'none';
        document.getElementById('rv-thumb-empty').style.display = 'flex';
        photoStatus.innerHTML = '<span style="color:#94a3b8">Chưa có ảnh</span>';
    }

    const noticeEl = document.getElementById('rv-notice');
    const noticeText = document.getElementById('rv-notice-text');
    if (selResId) {
        noticeEl.className = 'wi-notice';
        noticeText.innerHTML = 'Hệ thống sẽ gửi thông báo đẩy (push notification) tới ứng dụng của cư dân <strong>' + selResName + '</strong> ngay khi nhấn gửi.';
    } else {
        noticeEl.className = 'wi-notice';
        noticeText.innerHTML = 'Thông tin đã đầy đủ. Nhấn nút bên dưới để ghi nhận thông tin khách vào hệ thống.';
    }
}

/* =====================================================
   SUBMIT
   ===================================================== */
async function doSubmit() {
    const name     = document.getElementById('guest_name').value.trim();
    const aptId    = document.getElementById('apartment_id').value;
    const resident = selResName || document.getElementById('res_manual').value.trim();
    if (!name || !aptId || !resident) {
        showToast('Vui lòng kiểm tra lại thông tin.', 'error'); return;
    }
    const btn = document.getElementById('btn-submit');
    btn.disabled = true;
    btn.innerHTML = '<div class="wi-spin"></div> Đang gửi thông báo cho cư dân...';
    try {
        const r = await fetch(URL_STORE, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' },
            body: JSON.stringify({
                guest_name:            name,
                guest_phone:           document.getElementById('guest_phone').value.trim() || null,
                apartment_id:          parseInt(aptId),
                resident_to_meet:      resident,
                confirmed_by_resident: selResId || null,
                note:                  document.getElementById('note').value.trim() || null,
                vehicle_plate:         null,
                vehicle_type:          null,
                face_image:            document.getElementById('face_image_data').value || null,
                notify_resident:       !!selResId,
            }),
        });
        const data = await r.json();
        if (data.success) {
            showToast(data.message + (selResId ? ' · Đã thông báo cư dân thành công!' : ''), 'success');
            setTimeout(resetForm, 1800);
        } else {
            showToast(data.message || 'Có lỗi xảy ra.', 'error');
            btn.disabled = false;
            btn.innerHTML = '<i class="fa-solid fa-paper-plane"></i> GỬI THÔNG BÁO CHO CƯ DÂN & GHI NHẬN';
        }
    } catch {
        showToast('Lỗi kết nối tới máy chủ.', 'error');
        btn.disabled = false;
        btn.innerHTML = '<i class="fa-solid fa-paper-plane"></i> GỬI THÔNG BÁO CHO CƯ DÂN & GHI NHẬN';
    }
}

/* =====================================================
   RESET
   ===================================================== */
function resetForm() {
    ['guest_name','guest_phone','note'].forEach(id => document.getElementById(id).value = '');
    document.getElementById('apt_filter').value = '';
    document.getElementById('apartment_id').value = '';
    document.getElementById('res-box').style.display = 'none';
    document.getElementById('res-list').innerHTML = '';
    document.getElementById('res_manual').style.display = 'none';
    document.getElementById('res_manual').value = '';
    document.getElementById('confirmed_by_resident').value = '';
    selResId = ''; selResName = '';
    clearPhoto(); stopCam();
    document.getElementById('badge-1').classList.remove('show');
    document.getElementById('badge-2').classList.remove('show');
    setStep(1);
    window.scrollTo({ top: 0, behavior: 'smooth' });
}

/* =====================================================
   TOAST
   ===================================================== */
function showToast(msg, type) {
    const t = document.getElementById('wi-toast');
    t.className = 'wi-toast wi-toast--' + type;
    document.getElementById('toast-icon').innerHTML = type === 'success' ? '<i class="fa-solid fa-circle-check" style="color:#34d399"></i>' : '<i class="fa-solid fa-triangle-exclamation" style="color:#f87171"></i>';
    document.getElementById('toast-msg').textContent = msg;
    t.classList.add('show');
    clearTimeout(t._t); t._t = setTimeout(() => t.classList.remove('show'), 4200);
}

/* INIT */
setStep(1);
</script>
@endsection
