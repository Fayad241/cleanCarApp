import { useState, useEffect, useRef } from "react";

const steps = [
  { step: "01", title: "Réservez en ligne", desc: "Choisissez votre formule et réservez depuis l'app" },
  { step: "02", title: "Lavage premium", desc: "Nos équipes prennent soin de votre véhicule" },
  { step: "03", title: "Gagnez des points", desc: "10 pts pour chaque 1 000 FCFA dépensés" },
];

const rewards = [
  { points: 200, label: "−1 000 FCFA", icon: "◈", desc: "Réduction immédiate" },
  { points: 500, label: "−2 000 FCFA", icon: "◈", desc: "Réduction premium" },
  { points: 1000, label: "Lavage offert", icon: "★", desc: "Formule gratuite" },
];

export default function LoyaltySection() {
  const sectionRef = useRef<HTMLElement>(null);
  const [visible, setVisible] = useState(false);

  useEffect(() => {
    const observer = new IntersectionObserver(
      ([e]) => { if (e.isIntersecting) { setVisible(true); observer.disconnect(); } },
      { threshold: 0.1 }
    );
    if (sectionRef.current) observer.observe(sectionRef.current);
    return () => observer.disconnect();
  }, []);

  return (
    <section
      ref={sectionRef}
      className="relative overflow-hidden w-full"
      style={{
        backgroundColor: "var(--bg-primary)",
        padding: "var(--section-py) var(--container-px)",
      }}
    >
      {/* Background glow */}
      <div className="absolute top-1/2 right-[-10%] -translate-y-1/2 w-[500px] h-[500px] rounded-full pointer-events-none"
        style={{
          background: "radial-gradient(circle, var(--gold-glow) 0%, transparent 70%)",
          filter: "blur(60px)",
        }}
      />

      <div className="mx-auto relative" style={{ maxWidth: "var(--container-max)" }}>
        <div className="grid grid-cols-1 lg:grid-cols-2 items-center gap-20 loyalty-grid">

          {/* Left */}
          <div
            className="transition-all"
            style={{
              opacity: visible ? 1 : 0,
              transform: visible ? "translateX(0)" : "translateX(-30px)",
              transition: "all 0.8s ease",
            }}
          >
            <p 
              className="text-[0.69rem] uppercase tracking-wider mb-3"
              style={{
                color: "var(--green-primary)",
                letterSpacing: "0.3em",
                fontFamily: "var(--font-body)",
              }}
            >
              Programme fidélité
            </p>
            <h2 
              className="font-light mb-5"
              style={{
                fontFamily: "var(--font-display)",
                fontSize: "clamp(2rem, 4vw, 3rem)",
                color: "var(--text-primary)",
                lineHeight: 1.2,
              }}
            >
              Chaque lavage vous
              <br />
              <span className="text-gold">rapproche d'un cadeau</span>
            </h2>
            <p 
              className="text-sm leading-relaxed font-light max-w-[400px] mb-10"
              style={{
                color: "var(--text-secondary)",
                lineHeight: 1.8,
              }}
            >
              Accumulez des points à chaque passage et échangez-les contre des réductions exclusives ou un lavage entièrement gratuit.
            </p>

            {/* Steps */}
            <div className="flex flex-col gap-6 mb-10">
              {steps.map((s, i) => (
                <div key={i} className="flex items-start gap-5">
                  <div 
                    className="w-10 h-10 flex items-center justify-center flex-shrink-0"
                    style={{
                      border: "1px solid var(--border-gold)",
                      background: "var(--gold-glow)",
                    }}
                  >
                    <span style={{
                      color: "var(--green-primary)",
                      fontSize: "0.6rem",
                      fontFamily: "var(--font-display)",
                      fontWeight: 500,
                    }}>
                      {s.step}
                    </span>
                  </div>
                  <div>
                    <p className="text-sm font-normal mb-1" style={{ color: "var(--text-primary)" }}>
                      {s.title}
                    </p>
                    <p className="text-xs font-light" style={{ color: "var(--text-muted)" }}>
                      {s.desc}
                    </p>
                  </div>
                </div>
              ))}
            </div>

            <a
              href="/register"
              className="inline-flex items-center gap-3 px-8 py-3.5 text-xs uppercase tracking-wider no-underline transition-all"
              style={{
                border: "1px solid var(--border-gold)",
                color: "var(--green-primary)",
                fontSize: "0.7rem",
                letterSpacing: "0.18em",
                fontFamily: "var(--font-body)",
                transition: "all var(--transition-base)",
              }}
              onMouseEnter={(e) => {
                e.currentTarget.style.background = "var(--gold-glow)";
                e.currentTarget.style.borderColor = "var(--green-primary)";
              }}
              onMouseLeave={(e) => {
                e.currentTarget.style.background = "transparent";
                e.currentTarget.style.borderColor = "var(--border-gold)";
              }}
            >
              Créer mon compte gratuitement
              <span>→</span>
            </a>
          </div>

          {/* Right - Rewards */}
          <div
            className="transition-all"
            style={{
              opacity: visible ? 1 : 0,
              transform: visible ? "translateX(0)" : "translateX(30px)",
              transition: "all 0.8s ease 0.2s",
            }}
          >
            <div
              className="overflow-hidden"
              style={{
                border: "1px solid var(--border-gold)",
                background: "var(--bg-card)",
              }}
            >
              {/* Card header */}
              <div
                className="px-8 py-6"
                style={{
                  borderBottom: "1px solid var(--border-subtle)",
                  background: "var(--gold-glow)",
                }}
              >
                <p
                  className="text-[0.65rem] uppercase tracking-wider"
                  style={{
                    color: "var(--green-light)",
                    letterSpacing: "0.25em",
                    fontFamily: "var(--font-body)",
                  }}
                >
                  Vos récompenses disponibles
                </p>
              </div>

              {/* Rewards list */}
              {rewards.map((r, i) => (
                <div
                  key={i}
                  className="flex items-center justify-between px-8 py-6 transition-all"
                  style={{
                    borderBottom: i < rewards.length - 1 ? "1px solid var(--border-subtle)" : "none",
                    transition: "background var(--transition-base)",
                  }}
                  onMouseEnter={(e) => (e.currentTarget.style.background = "var(--bg-card-hover)")}
                  onMouseLeave={(e) => (e.currentTarget.style.background = "transparent")}
                >
                  <div className="flex items-center gap-4">
                    <div
                      className="w-9 h-9 flex items-center justify-center text-base"
                      style={{
                        color: "var(--green-primary)",
                        border: "1px solid var(--border-gold)",
                      }}
                    >
                      {r.icon}
                    </div>
                    <div>
                      <p className="text-sm font-normal" style={{ color: "var(--text-primary)" }}>
                        {r.label}
                      </p>
                      <p className="text-xs mt-1" style={{ color: "var(--text-muted)" }}>
                        {r.desc}
                      </p>
                    </div>
                  </div>
                  <div
                    className="px-3 py-1.5"
                    style={{
                      border: "1px solid var(--border-gold)",
                      background: "var(--gold-glow)",
                    }}
                  >
                    <span style={{
                      color: "var(--green-primary)",
                      fontSize: "0.7rem",
                      fontFamily: "var(--font-display)",
                      fontWeight: 500,
                    }}>
                      {r.points} pts
                    </span>
                  </div>
                </div>
              ))}

              {/* Card footer */}
              <div
                className="px-8 py-5 flex items-center gap-2"
                style={{
                  background: "var(--bg-secondary)",
                }}
              >
                <span className="text-[0.6rem]" style={{ color: "var(--green-primary)" }}>●</span>
                <p className="text-xs" style={{ color: "var(--text-muted)" }}>
                  10 points pour chaque 1 000 FCFA dépensés
                </p>
              </div>
            </div>
          </div>
        </div>
      </div>

      <style>{`
        @media (max-width: 1024px) {
          .loyalty-grid { gap: 3rem !important; }
        }
      `}</style>
    </section>
  );
}