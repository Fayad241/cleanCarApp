import { useState, useEffect, useRef } from "react";

interface Service {
  id: string;
  name: string;
  pricing: { vehicule_size: string; price: number; duration_minutes: number }[];
}

interface TimeSlot {
  time: string;
  duration_minutes: number;
  end_time: string;
}

const VEHICULE_SIZES = [
  { value: "small", label: "Petite · Citadine" },
  { value: "medium", label: "Moyenne · Berline" },
  { value: "large", label: "Grande · SUV" },
  { value: "xlarge", label: "Très grande · Van" },
  { value: "motorcycle", label: "Moto" },
];

export default function BookingSection() {
  const sectionRef = useRef<HTMLElement>(null);
  const [visible, setVisible] = useState(false);
  const [services, setServices] = useState<Service[]>([]);
  const [slots, setSlots] = useState<TimeSlot[]>([]);
  const [slotsLoading, setSlotsLoading] = useState(false);
  const [form, setForm] = useState({ service_id: "", vehicule_size: "", date: "", time: "" });

  const today = new Date().toISOString().split("T")[0];

  useEffect(() => {
    const observer = new IntersectionObserver(
      ([e]) => { if (e.isIntersecting) { setVisible(true); observer.disconnect(); } },
      { threshold: 0.15 }
    );
    if (sectionRef.current) observer.observe(sectionRef.current);
    return () => observer.disconnect();
  }, []);

  useEffect(() => {
    fetch(`${import.meta.env.VITE_API_URL}/services`)
      .then((r) => r.json())
      .then((d) => setServices(d.data?.services || []));
  }, []);

  useEffect(() => {
    if (!form.service_id || !form.vehicule_size || !form.date) return;
    setSlotsLoading(true);
    fetch(`${import.meta.env.VITE_API_URL}/available-slots?service_id=${form.service_id}&vehicule_size=${form.vehicule_size}&date=${form.date}`)
      .then((r) => r.json())
      .then((d) => { setSlots(d.data?.slots || []); setForm((f) => ({ ...f, time: "" })); })
      .finally(() => setSlotsLoading(false));
  }, [form.service_id, form.vehicule_size, form.date]);

  const selectedService = services.find((s) => s.id === form.service_id);
  const selectedPricing = selectedService?.pricing.find((p) => p.vehicule_size === form.vehicule_size);
  const isComplete = form.service_id && form.vehicule_size && form.date && form.time;

  const fieldStyle: React.CSSProperties = {
    background: "var(--bg-card)",
    border: "1px solid var(--border-light)",
    color: "var(--text-primary)",
    fontSize: "0.875rem",
    padding: "0.875rem 1rem",
    width: "100%",
    fontFamily: "var(--font-body)",
    fontWeight: 300,
    outline: "none",
    transition: "border-color var(--transition-base)",
    appearance: "none",
  };

  const labelStyle: React.CSSProperties = {
    display: "block",
    color: "var(--text-secondary)",
    fontSize: "0.61rem",
    letterSpacing: "0.2em",
    textTransform: "uppercase",
    marginBottom: "0.5rem",
    fontFamily: "var(--font-body)",
  };

  return (
    <section
      ref={sectionRef}
      id="reservation"
      className="w-full"
      style={{
        backgroundColor: "var(--bg-secondary)",
        padding: "var(--section-py) var(--container-px)",
      }}
    >
      <div className="mx-auto" style={{ maxWidth: "var(--container-max)" }}>

        {/* Header */}
        <div
          className="text-center mb-14 transition-all"
          style={{
            opacity: visible ? 1 : 0,
            transform: visible ? "translateY(0)" : "translateY(24px)",
            transition: "all 0.7s ease",
          }}
        >
          <p 
            className="text-[0.69rem] uppercase tracking-wider mb-3"
            style={{ 
              color: "var(--green-primary)", 
              letterSpacing: "0.3em", 
              fontFamily: "var(--font-body)" 
            }}
          >
            Réservation rapide
          </p>
          <h2
            className="font-light"
            style={{
              fontFamily: "var(--font-display)",
              fontSize: "clamp(2rem, 4vw, 3rem)",
              color: "var(--text-primary)",
              lineHeight: 1.2,
            }}
          >
            Choisissez votre{" "}
            <span className="text-gold">créneau</span>
          </h2>
        </div>

        {/* Form Card */}
        <div
          className="relative md:p-12 py-12 px-6 transition-all lg:mx-24"
          style={{
            background: "var(--bg-card)",
            border: "1px solid var(--border-gold)",
            opacity: visible ? 1 : 0,
            transform: visible ? "translateY(0)" : "translateY(32px)",
            transition: "all 0.7s ease 0.15s",
          }}
        >
          {/* Corner accents */}
          {[
            { top: 0, left: 0, borderLeft: "2px solid var(--green-primary)", borderTop: "2px solid var(--green-primary)" },
            { bottom: 0, right: 0, borderRight: "2px solid var(--green-primary)", borderBottom: "2px solid var(--green-primary)" },
          ].map((style, i) => (
            <div 
              key={i} 
              className="absolute w-5 h-5" 
              style={style} 
            />
          ))}

          {/* Fields Grid */}
          <div className="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">

            {/* Service */}
            <div>
              <label style={labelStyle}>Service de lavage</label>
              <select
                value={form.service_id}
                onChange={(e) => setForm((f) => ({ ...f, service_id: e.target.value, time: "" }))}
                className="w-full cursor-pointer"
                style={fieldStyle}
                onFocus={(e) => (e.target.style.borderColor = "var(--border-gold)")}
                onBlur={(e) => (e.target.style.borderColor = "var(--border-light)")}
              >
                <option value="" style={{ background: "var(--bg-card)" }}>Sélectionner...</option>
                {services.map((s) => (
                  <option key={s.id} value={s.id} style={{ background: "var(--bg-card)" }}>{s.name}</option>
                ))}
              </select>
            </div>

            {/* Taille */}
            <div>
              <label style={labelStyle}>Taille du véhicule</label>
              <select
                value={form.vehicule_size}
                onChange={(e) => setForm((f) => ({ ...f, vehicule_size: e.target.value, time: "" }))}
                className="w-full cursor-pointer"
                style={fieldStyle}
                onFocus={(e) => (e.target.style.borderColor = "var(--border-gold)")}
                onBlur={(e) => (e.target.style.borderColor = "var(--border-light)")}
              >
                <option value="" style={{ background: "var(--bg-card)" }}>Sélectionner...</option>
                {VEHICULE_SIZES.map((s) => (
                  <option key={s.value} value={s.value} style={{ background: "var(--bg-card)" }}>{s.label}</option>
                ))}
              </select>
            </div>

            {/* Date */}
            <div>
              <label style={labelStyle}>Date souhaitée</label>
              <input
                type="date"
                min={today}
                value={form.date}
                onChange={(e) => setForm((f) => ({ ...f, date: e.target.value, time: "" }))}
                className="w-full cursor-pointer"
                style={fieldStyle}
                onFocus={(e) => (e.target.style.borderColor = "var(--border-gold)")}
                onBlur={(e) => (e.target.style.borderColor = "var(--border-light)")}
              />
            </div>

            {/* Créneau */}
            <div>
              <label style={labelStyle}>Créneau horaire</label>
              {slotsLoading ? (
                <div style={{ ...fieldStyle, color: "var(--text-muted)", display: "flex", alignItems: "center", gap: "0.5rem" }}>
                  <span 
                    className="inline-block w-3 h-3 rounded-full animate-spin"
                    style={{ 
                      border: "1px solid var(--border-gold)", 
                      borderTopColor: "var(--green-primary)",
                      animation: "spin-slow 0.8s linear infinite"
                    }} 
                  />
                  Chargement...
                </div>
              ) : slots.length > 0 ? (
                <select
                  value={form.time}
                  onChange={(e) => setForm((f) => ({ ...f, time: e.target.value }))}
                  className="w-full cursor-pointer"
                  style={fieldStyle}
                  onFocus={(e) => (e.target.style.borderColor = "var(--border-gold)")}
                  onBlur={(e) => (e.target.style.borderColor = "var(--border-light)")}
                >
                  <option value="" style={{ background: "var(--bg-card)" }}>Choisir un créneau</option>
                  {slots.map((slot) => (
                    <option key={slot.time} value={slot.time} style={{ background: "var(--bg-card)" }}>
                      {slot.time} → {slot.end_time} ({slot.duration_minutes} min)
                    </option>
                  ))}
                </select>
              ) : (
                <div style={{ ...fieldStyle, color: "var(--text-muted)" }}>
                  {form.service_id && form.vehicule_size && form.date
                    ? "Aucun créneau ce jour"
                    : "Remplissez les champs d'abord"}
                </div>
              )}
            </div>
          </div>

          {/* Résumé Prix */}
          {selectedPricing && (
            <div
              className="flex items-center justify-between flex-wrap gap-4 p-5 mb-8"
              style={{
                background: "var(--gold-glow)",
                border: "1px solid var(--border-gold)",
                animation: "fadeUp 0.4s ease forwards",
              }}
            >
              <div>
                <p style={{ color: "var(--text-muted)", fontSize: "0.6rem", letterSpacing: "0.2em", textTransform: "uppercase", marginBottom: "0.25rem" }}>
                  Récapitulatif
                </p>
                <p style={{ color: "var(--text-primary)", fontSize: "0.9rem", fontFamily: "var(--font-body)" }}>
                  {selectedService?.name} · {VEHICULE_SIZES.find((s) => s.value === form.vehicule_size)?.label}
                </p>
              </div>
              <div className="text-right">
                <p style={{ fontFamily: "var(--font-display)", fontSize: "1.75rem", fontWeight: 500, color: "var(--green-light)", lineHeight: 1 }}>
                  {selectedPricing.price.toLocaleString()} FCFA
                </p>
                <p style={{ color: "var(--text-muted)", fontSize: "0.65rem", marginTop: "0.2rem" }}>
                  Durée : {selectedPricing.duration_minutes} min
                </p>
              </div>
            </div>
          )}

          {/* Submit */}
          <div className="flex flex-col items-center gap-5">
            <button
              disabled={!isComplete}
              onClick={() => {
                if (!isComplete) return;
                window.location.href = `/reservation?service_id=${form.service_id}&vehicule_size=${form.vehicule_size}&date=${form.date}&time=${form.time}`;
              }}
              className={`relative px-14 py-4 text-xs font-bold uppercase tracking-wider transition-all duration-300 ${
                isComplete 
                  ? 'hover:shadow-[var(--glow-green)] hover:scale-[1.02] cursor-pointer' 
                  : 'cursor-not-allowed opacity-70'
              }`}
              style={{
                background: isComplete 
                  ? "var(--gold-gradient)" 
                  : "transparent",
                border: isComplete 
                  ? "none" 
                  : "1px solid var(--border-subtle)",
                color: isComplete 
                  ? "var(--bg-primary)" 
                  : "var(--text-muted)",
                fontSize: "0.72rem",
                fontWeight: 700,
                letterSpacing: "0.2em",
                fontFamily: "var(--font-body)",
                borderRadius: "4px",
                boxShadow: isComplete ? "var(--shadow-gold)" : "none",
                position: "relative",
                overflow: "hidden",
              }}
            >
              {/* Ligne animée pour état désactivé */}
              {!isComplete && (
                <span className="absolute inset-0 border border-[var(--border-gold)]/30" />
              )}
              
              <span className="relative z-10 flex items-center gap-2">
                {!isComplete && <span className="text-[var(--text-faint)]">◌</span>}
                {isComplete ? "Réserver maintenant →" : "Formulaire incomplet"}
              </span>
            </button>
            
            {/* Indicateurs de progression */}
            <div className="flex items-center gap-3">
              {["service", "taille", "date", "horaire"].map((field, idx) => {
                const isFilled = 
                  (field === "service" && form.service_id) ||
                  (field === "taille" && form.vehicule_size) ||
                  (field === "date" && form.date) ||
                  (field === "horaire" && form.time);
                
                return (
                  <div key={field} className="flex items-center gap-1.5">
                    <div 
                      className={`w-2 h-2 rounded-full transition-all duration-300 ${
                        isFilled ? 'bg-[var(--green-primary)]' : 'bg-[var(--border-subtle)]'
                      }`}
                    />
                    <span className="text-[0.6rem] uppercase tracking-wider text-[var(--text-secondary)] hidden sm:inline">
                      {field}
                    </span>
                  </div>
                );
              })}
            </div>
          </div>
        </div>
      </div>

      <style>{`
        @keyframes spin-slow {
          from { transform: rotate(0deg); }
          to   { transform: rotate(360deg); }
        }
      `}</style>
    </section>
  );
}