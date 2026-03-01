 import { useState, useEffect, useRef } from "react";

const TESTIMONIALS = [
  {
    id: 1,
    name: "Koffi Mensah",
    role: "Client fidèle · 8 mois",
    vehicule: "Toyota RAV4",
    rating: 5,
    text: "Vraiment impressionné par le résultat. Ma voiture ressort comme neuve à chaque passage. Le formulaire de réservation en ligne m'a facilité la vie, plus besoin d'appeler.",
    initials: "KM",
    color: "#3cb371",
  },
  {
    id: 2,
    name: "Aïcha Bello",
    role: "Cliente · 3 mois",
    vehicule: "Honda CB500",
    rating: 5,
    text: "Ils s'occupent aussi des motos, ce qui est rare à Cotonou. Mon CB500 n'a jamais été aussi propre. L'équipe est sérieuse et ponctuelle, je recommande sans hésiter.",
    initials: "AB",
    color: "#3f9e8f",
  },
  {
    id: 3,
    name: "Rodrigue Adjovi",
    role: "Client fidèle · 1 an",
    vehicule: "Mercedes GLC",
    rating: 5,
    text: "Pour une Mercedes, je ne fais pas confiance à n'importe qui. CleanCar Pro utilise des produits premium adaptés. Le programme de points est un vrai bonus.",
    initials: "RA",
    color: "#9d7fd1",
  },
  {
    id: 4,
    name: "Fatou Diallo",
    role: "Cliente · 5 mois",
    vehicule: "Hyundai Tucson",
    rating: 5,
    text: "Le détailing complet que j'ai fait était bluffant. L'intérieur sentait le neuf, les jantes chromées brillaient. Et le prix reste très raisonnable pour la qualité.",
    initials: "FD",
    color: "#e6b450",
  },
  {
    id: 5,
    name: "Séraphin Hounsou",
    role: "Client · 2 mois",
    vehicule: "Yamaha MT-07",
    rating: 5,
    text: "Rapide, efficace, propre. J'ai réservé le soir pour le lendemain matin, tout s'est passé exactement comme prévu. Le système de rappel SMS est vraiment pratique.",
    initials: "SH",
    color: "#ffa07a",
  },
  {
    id: 6,
    name: "Mariette Agossou",
    role: "Cliente fidèle · 10 mois",
    vehicule: "Peugeot 308",
    rating: 5,
    text: "J'ai essayé plusieurs stations à Cotonou avant de trouver CleanCar. La différence est nette — personnel attentionné, résultat impeccable, et les points fidélité font la différence.",
    initials: "MA",
    color: "#40a69a",
  },
];

export default function TestimonialsSection() {
  const sectionRef = useRef<HTMLDivElement>(null);
  const trackRef = useRef<HTMLDivElement>(null);
  const [visible, setVisible] = useState(false);
  const [active, setActive] = useState(0);
  const [isPaused, setIsPaused] = useState(false);
  const intervalRef = useRef<ReturnType<typeof setInterval> | null>(null);

  // Reveal on scroll
  useEffect(() => {
    const observer = new IntersectionObserver(
      ([e]) => { if (e.isIntersecting) { setVisible(true); observer.disconnect(); } },
      { threshold: 0.1 }
    );
    if (sectionRef.current) observer.observe(sectionRef.current);
    return () => observer.disconnect();
  }, []);

  // Auto-scroll
  useEffect(() => {
    if (isPaused) return;
    intervalRef.current = setInterval(() => {
      setActive(a => (a + 1) % TESTIMONIALS.length);
    }, 4000);
    return () => { if (intervalRef.current) clearInterval(intervalRef.current); };
  }, [isPaused]);

  const goTo = (i: number) => {
    setActive(i);
    setIsPaused(true);
    setTimeout(() => setIsPaused(false), 8000);
  };

  const prev = () => goTo((active - 1 + TESTIMONIALS.length) % TESTIMONIALS.length);
  const next = () => goTo((active + 1) % TESTIMONIALS.length);

  // Get visible cards (active + neighbours)
  const getCards = () => {
    const len = TESTIMONIALS.length;
    return [
      (active - 1 + len) % len,
      active,
      (active + 1) % len,
    ];
  };

  const cards = getCards();

  return (
    <div
      ref={sectionRef}
      style={{
        backgroundColor: "var(--bg-primary)",
        padding: "6rem var(--container-px)",
        position: "relative",
        overflow: "hidden",
      }}
    >
      {/* Background grid */}
      <div className="hybrid-grid" style={{ position: "absolute", inset: 0, opacity: 0.4 }} />

      {/* Glow blobs */}
      <div style={{
        position: "absolute", top: "10%", left: "-5%",
        width: "400px", height: "400px", borderRadius: "50%",
        background: "radial-gradient(circle, rgba(60,179,113,0.07) 0%, transparent 70%)",
        filter: "blur(60px)", pointerEvents: "none",
      }} />
      <div style={{
        position: "absolute", bottom: "10%", right: "-5%",
        width: "350px", height: "350px", borderRadius: "50%",
        background: "radial-gradient(circle, rgba(63,158,143,0.07) 0%, transparent 70%)",
        filter: "blur(60px)", pointerEvents: "none",
      }} />

      <div style={{ maxWidth: "var(--container-max)", margin: "0 auto", position: "relative" }}>

        {/* Header */}
        <div style={{
          display: "flex",
          justifyContent: "space-between",
          alignItems: "flex-end",
          marginBottom: "3.5rem",
          flexWrap: "wrap",
          gap: "1.5rem",
          opacity: visible ? 1 : 0,
          transform: visible ? "translateY(0)" : "translateY(24px)",
          transition: "all 0.7s ease",
        }}>
          <div>
            <p style={{
              fontSize: "0.69rem", fontWeight: 500,
              letterSpacing: "0.3em", textTransform: "uppercase",
              color: "var(--green-primary)", marginBottom: "0.75rem",
              fontFamily: "var(--font-body)",
            }}>
              Témoignages
            </p>
            <h2 style={{
              fontFamily: "var(--font-display)",
              fontSize: "clamp(1.75rem, 4vw, 3rem)",
              fontWeight: 400,
              color: "var(--text-primary)",
              lineHeight: 1.15,
            }}>
              Ce que disent{" "}
              <span className="text-gold">nos clients</span>
            </h2>
          </div>

          {/* Global rating */}
          <div style={{
            display: "flex", alignItems: "center", gap: "1.25rem",
            padding: "1rem 1.5rem",
            background: "var(--bg-card)",
            border: "1px solid var(--border-gold)",
            borderRadius: "var(--radius-lg)",
          }}>
            <div style={{ textAlign: "center" }}>
              <p style={{
                fontFamily: "var(--font-display)",
                fontSize: "2.25rem", fontWeight: 500,
                color: "var(--green-light)", lineHeight: 1,
              }}>4.9</p>
              <div style={{ display: "flex", gap: "2px", justifyContent: "center", marginTop: "4px" }}>
                {[1,2,3,4,5].map(s => (
                  <span key={s} style={{ color: "#e6b450", fontSize: "0.65rem" }}>★</span>
                ))}
              </div>
            </div>
            <div style={{ width: "1px", height: "40px", background: "var(--border-subtle)" }} />
            <div>
              <p style={{ fontSize: "0.8rem", fontWeight: 400, color: "var(--text-secondary)" }}>200+ avis</p>
              <p style={{ fontSize: "0.7rem", fontWeight: 300, color: "var(--text-muted)", marginTop: "2px" }}>clients satisfaits</p>
            </div>
          </div>
        </div>

        {/* Cards carousel */}
        <div
          style={{
            position: "relative",
            opacity: visible ? 1 : 0,
            transform: visible ? "translateY(0)" : "translateY(32px)",
            transition: "all 0.7s ease 0.15s",
          }}
          onMouseEnter={() => setIsPaused(true)}
          onMouseLeave={() => setIsPaused(false)}
        >
          {/* 3-card layout */}
          <div style={{
            display: "grid",
            gridTemplateColumns: "1fr 1.08fr 1fr",
            gap: "1.25rem",
            alignItems: "center",
          }} className="testimonials-grid">
            {cards.map((idx, pos) => {
              const t = TESTIMONIALS[idx];
              const isCenter = pos === 1;
              return (
                <div
                  key={t.id}
                  onClick={() => !isCenter && goTo(idx)}
                  style={{
                    padding: isCenter ? "2.25rem" : "1.75rem",
                    background: isCenter ? "var(--bg-card)" : "var(--bg-secondary)",
                    border: `1px solid ${isCenter ? "var(--border-gold-bright)" : "var(--border-subtle)"}`,
                    borderRadius: "var(--radius-lg)",
                    cursor: isCenter ? "default" : "pointer",
                    transition: "all 0.5s cubic-bezier(.16,1,.3,1)",
                    opacity: isCenter ? 1 : 0.55,
                    transform: isCenter ? "scale(1)" : "scale(0.96)",
                    boxShadow: isCenter ? "var(--shadow-md), var(--glow-soft)" : "none",
                    position: "relative",
                    overflow: "hidden",
                  }}
                  onMouseEnter={e => {
                    if (!isCenter) e.currentTarget.style.opacity = "0.8";
                  }}
                  onMouseLeave={e => {
                    if (!isCenter) e.currentTarget.style.opacity = "0.55";
                  }}
                >
                  {/* Top accent line on center */}
                  {isCenter && (
                    <div style={{
                      position: "absolute", top: 0, left: 0, right: 0, height: "2px",
                      background: `linear-gradient(90deg, transparent, ${t.color}, transparent)`,
                    }} />
                  )}

                  {/* Quote mark */}
                  <div style={{
                    fontFamily: "Georgia, serif",
                    fontSize: "4rem", lineHeight: 0.8,
                    color: isCenter ? t.color : "var(--border-subtle)",
                    marginBottom: "1rem",
                    opacity: isCenter ? 0.4 : 0.3,
                    transition: "color 0.5s ease",
                    userSelect: "none",
                  }}>
                    "
                  </div>

                  {/* Stars */}
                  <div style={{ display: "flex", gap: "3px", marginBottom: "1rem" }}>
                    {[1,2,3,4,5].map(s => (
                      <span key={s} style={{
                        color: s <= t.rating ? "#e6b450" : "var(--border-subtle)",
                        fontSize: isCenter ? "0.75rem" : "0.65rem",
                      }}>★</span>
                    ))}
                  </div>

                  {/* Text */}
                  <p style={{
                    fontSize: isCenter ? "0.925rem" : "0.825rem",
                    fontWeight: 300,
                    color: isCenter ? "var(--text-secondary)" : "var(--text-muted)",
                    lineHeight: 1.8,
                    marginBottom: "1.5rem",
                    transition: "all 0.5s ease",
                    display: "-webkit-box",
                    WebkitLineClamp: isCenter ? 999 : 4,
                    WebkitBoxOrient: "vertical",
                    overflow: "hidden",
                  }}>
                    {t.text}
                  </p>

                  {/* Author */}
                  <div style={{
                    display: "flex", alignItems: "center", gap: "0.875rem",
                    paddingTop: "1.25rem",
                    borderTop: `1px solid ${isCenter ? "var(--border-subtle)" : "var(--border-faint)"}`,
                  }}>
                    {/* Avatar */}
                    <div style={{
                      width: isCenter ? "42px" : "36px",
                      height: isCenter ? "42px" : "36px",
                      borderRadius: "50%",
                      background: `linear-gradient(135deg, ${t.color}33, ${t.color}66)`,
                      border: `1.5px solid ${t.color}55`,
                      display: "flex", alignItems: "center", justifyContent: "center",
                      flexShrink: 0,
                      transition: "all 0.5s ease",
                    }}>
                      <span style={{
                        fontSize: isCenter ? "0.7rem" : "0.6rem",
                        fontWeight: 600,
                        color: t.color,
                      }}>{t.initials}</span>
                    </div>
                    <div>
                      <p style={{
                        fontSize: isCenter ? "0.875rem" : "0.78rem",
                        fontWeight: 400,
                        color: isCenter ? "var(--text-primary)" : "var(--text-secondary)",
                        transition: "all 0.5s ease",
                      }}>{t.name}</p>
                      <p style={{
                        fontSize: "0.68rem", fontWeight: 300,
                        color: "var(--text-faint)", marginTop: "1px",
                      }}>{t.vehicule}</p>
                    </div>
                    {isCenter && (
                      <div style={{
                        marginLeft: "auto",
                        padding: "0.2rem 0.6rem",
                        background: `${t.color}18`,
                        border: `1px solid ${t.color}33`,
                        borderRadius: "var(--radius-full)",
                        fontSize: "0.58rem",
                        fontWeight: 500,
                        letterSpacing: "0.12em",
                        textTransform: "uppercase",
                        color: t.color,
                      }}>
                        {t.role.split(" · ")[1]}
                      </div>
                    )}
                  </div>
                </div>
              );
            })}
          </div>

          {/* Nav buttons */}
          <button
            onClick={prev}
            style={{
              position: "absolute",
              left: "-20px", top: "50%", transform: "translateY(-50%)",
              width: "40px", height: "40px",
              background: "var(--bg-card)",
              border: "1px solid var(--border-gold)",
              borderRadius: "50%",
              color: "var(--green-primary)",
              fontSize: "1rem",
              cursor: "pointer",
              display: "flex", alignItems: "center", justifyContent: "center",
              transition: "all var(--transition-fast)",
              boxShadow: "var(--shadow-sm)",
              zIndex: 10,
            }}
            onMouseEnter={e => {
              e.currentTarget.style.background = "var(--bg-card-hover)";
              e.currentTarget.style.boxShadow = "var(--glow-soft)";
            }}
            onMouseLeave={e => {
              e.currentTarget.style.background = "var(--bg-card)";
              e.currentTarget.style.boxShadow = "var(--shadow-sm)";
            }}
          >
            ‹
          </button>
          <button
            onClick={next}
            style={{
              position: "absolute",
              right: "-20px", top: "50%", transform: "translateY(-50%)",
              width: "40px", height: "40px",
              background: "var(--bg-card)",
              border: "1px solid var(--border-gold)",
              borderRadius: "50%",
              color: "var(--green-primary)",
              fontSize: "1rem",
              cursor: "pointer",
              display: "flex", alignItems: "center", justifyContent: "center",
              transition: "all var(--transition-fast)",
              boxShadow: "var(--shadow-sm)",
              zIndex: 10,
            }}
            onMouseEnter={e => {
              e.currentTarget.style.background = "var(--bg-card-hover)";
              e.currentTarget.style.boxShadow = "var(--glow-soft)";
            }}
            onMouseLeave={e => {
              e.currentTarget.style.background = "var(--bg-card)";
              e.currentTarget.style.boxShadow = "var(--shadow-sm)";
            }}
          >
            ›
          </button>
        </div>

        {/* Dots */}
        <div style={{
          display: "flex", justifyContent: "center",
          gap: "0.5rem", marginTop: "2rem",
          opacity: visible ? 1 : 0,
          transition: "opacity 0.7s ease 0.3s",
        }}>
          {TESTIMONIALS.map((_, i) => (
            <button
              key={i}
              onClick={() => goTo(i)}
              style={{
                width: i === active ? "24px" : "8px",
                height: "8px",
                borderRadius: "4px",
                background: i === active ? "var(--green-primary)" : "var(--border-subtle)",
                border: "none", cursor: "pointer", padding: 0,
                transition: "all 0.4s cubic-bezier(.16,1,.3,1)",
              }}
            />
          ))}
        </div>

        {/* CTA bottom */}
        <div style={{
          marginTop: "3.5rem",
          display: "flex",
          flexDirection: "column",
          alignItems: "center",
          gap: "1rem",
          opacity: visible ? 1 : 0,
          transition: "opacity 0.7s ease 0.4s",
        }}>
          <p style={{
            fontSize: "0.825rem", fontWeight: 300,
            color: "var(--text-muted)",
          }}>
            Rejoignez plus de 200 clients satisfaits à Cotonou
          </p>
          <a
            href="/register"
            style={{
              display: "inline-flex", alignItems: "center", gap: "0.6rem",
              padding: "0.975rem 2.25rem",
              background: "var(--gold-gradient)",
              color: "#fff",
              fontSize: "0.72rem", fontWeight: 600,
              letterSpacing: "0.18em", textTransform: "uppercase",
              fontFamily: "var(--font-body)",
              borderRadius: "0",
              textDecoration: "none",
              boxShadow: "var(--shadow-gold), 0 10px 20px rgba(0,0,0,0.3)",
              transition: "transform var(--transition-base), box-shadow var(--transition-base)",
            }}
            onMouseEnter={e => {
              e.currentTarget.style.transform = "translateY(-2px)";
              e.currentTarget.style.boxShadow = "0 12px 40px rgba(60,179,113,0.3)";
            }}
            onMouseLeave={e => {
              e.currentTarget.style.transform = "translateY(0)";
              e.currentTarget.style.boxShadow = "var(--glow-green)";
            }}
          >
            Réserver mon lavage
            <span>→</span>
          </a>
        </div>
      </div>

      <style>{`
        @media (max-width: 768px) {
          .testimonials-grid {
            grid-template-columns: 1fr !important;
          }
          .testimonials-grid > div:not(:nth-child(2)) {
            display: none;
          }
        }
      `}</style>
    </div>
  );
}
