import { useState, useEffect, useRef } from "react";

export default function AboutSection() {
  const sectionRef = useRef<HTMLDivElement>(null);
  const [visible, setVisible] = useState(false);

  useEffect(() => {
    const observer = new IntersectionObserver(
      ([e]) => { if (e.isIntersecting) { setVisible(true); observer.disconnect(); } },
      { threshold: 0.15 }
    );
    if (sectionRef.current) observer.observe(sectionRef.current);
    return () => observer.disconnect();
  }, []);

  const reveal = (delay = 0): React.CSSProperties => ({
    opacity: visible ? 1 : 0,
    transform: visible ? "translateY(0)" : "translateY(22px)",
    transition: `opacity 0.7s ease ${delay}s, transform 0.7s ease ${delay}s`,
  });

  return (
    <div
      ref={sectionRef}
      style={{
        backgroundColor: "var(--bg-secondary)",
        padding: "6rem var(--container-px)",
        position: "relative",
        overflow: "hidden",
      }}
    >
      {/* Top border accent */}
      <div style={{
        position: "absolute", top: 0, left: 0, right: 0, height: "1px",
        background: "linear-gradient(90deg, transparent, var(--border-gold), transparent)",
      }} />

      {/* Glow */}
      <div style={{
        position: "absolute", top: "-80px", right: "10%",
        width: "360px", height: "360px", borderRadius: "50%",
        background: "radial-gradient(circle, rgba(60,179,113,0.06) 0%, transparent 70%)",
        filter: "blur(50px)", pointerEvents: "none",
      }} />

      <div style={{ maxWidth: "var(--container-max)", margin: "0 auto", position: "relative" }}>
        <div style={{
          display: "grid",
          gridTemplateColumns: "1fr 1fr",
          gap: "5rem",
          alignItems: "center",
        }} className="about-grid">

          {/* ── Left — Image + badge ── */}
          <div style={{ position: "relative", ...reveal(0) }}>

            {/* Image principale */}
            <div style={{
              position: "relative",
              borderRadius: "var(--radius-lg)",
              overflow: "hidden",
              aspectRatio: "4/3",
           }}>
              <img
                src="https://images.unsplash.com/photo-1520340356584-f9917d1eea6f?w=800&q=80"
                alt="Équipe CleanCar Pro"
                style={{
                  width: "100%", height: "100%",
                  objectFit: "cover", objectPosition: "center",
                  opacity: 0.7,
                  transition: "opacity var(--transition-slow), transform var(--transition-slow)",
                }}
                onMouseEnter={e => {
                  e.currentTarget.style.opacity = "0.9";
                  e.currentTarget.style.transform = "scale(1.03)";
                }}
                onMouseLeave={e => {
                  e.currentTarget.style.opacity = "0.7";
                  e.currentTarget.style.transform = "scale(1)";
                }}
              />
              {/* Overlay gradient */}
              <div style={{
                position: "absolute", inset: 0,
                background: "linear-gradient(to top, var(--bg-secondary) 0%, transparent 50%)",
              }} />
              {/* Top accent border */}
              <div style={{
                position: "absolute", top: 0, left: 0, right: 0, height: "2px",
                background: "var(--gradient-sunset)",
              }} />
            </div>

            {/* Floating badge — années d'expérience */}
            <div style={{
              position: "absolute",
              bottom: "-1.25rem",
              right: "-1.25rem",
              padding: "1.25rem 1.5rem",
              background: "var(--bg-card)",
              border: "1px solid var(--border-gold)",
              borderRadius: "var(--radius-lg)",
              boxShadow: "var(--shadow-md), var(--glow-soft)",
              textAlign: "center",
              minWidth: "130px",
            }}>
              <p style={{
                fontFamily: "var(--font-display)",
                fontSize: "2.5rem", fontWeight: 500,
                color: "var(--green-light)", lineHeight: 1,
                marginBottom: "0.2rem",
              }}>5+</p>
              <p style={{
                fontSize: "0.6rem", fontWeight: 500,
                letterSpacing: "0.2em", textTransform: "uppercase",
                color: "var(--text-muted)",
              }}>Ans d'expérience</p>
            </div>

            {/* Corner decoration */}
            <div style={{
              position: "absolute", top: "-10px", left: "-10px",
              width: "28px", height: "28px",
              borderLeft: "2px solid var(--green-primary)",
              borderTop: "2px solid var(--green-primary)",
              borderRadius: "2px 0 0 0",
              opacity: 0.5,
            }} />
          </div>

          {/* ── Right — Content ── */}
          <div>
            <p style={{
              fontSize: "0.69rem", fontWeight: 500,
              letterSpacing: "0.3em", textTransform: "uppercase",
              color: "var(--green-primary)",
              marginBottom: "0.75rem",
              fontFamily: "var(--font-body)",
              ...reveal(0.1),
            }}>
              À propos de nous
            </p>

            <h2 style={{
              fontFamily: "var(--font-display)",
              fontSize: "clamp(1.75rem, 3.5vw, 2.75rem)",
              fontWeight: 400,
              color: "var(--text-primary)",
              lineHeight: 1.15,
              marginBottom: "1.5rem",
              ...reveal(0.15),
            }}>
              Bien plus qu'une{" "}
              <span className="text-gold">station de lavage</span>
            </h2>

            <p style={{
              fontSize: "1rem", fontWeight: 300,
              color: "var(--text-secondary)", lineHeight: 1.85,
              marginBottom: "1.25rem",
              ...reveal(0.2),
            }}>
              Fondée à Cotonou avec une seule ambition : offrir à chaque véhicule — voiture, moto, SUV ou van — un soin digne des plus grandes enseignes, au meilleur prix.
            </p>

            <p style={{
              fontSize: "1rem", fontWeight: 300,
              color: "var(--text-muted)", lineHeight: 1.85,
              marginBottom: "2.25rem",
              ...reveal(0.25),
            }}>
              Une équipe formée, des produits certifiés, et une technologie de réservation pensée pour simplifier votre quotidien.
            </p>

            {/* 3 mini-stats */}
            <div style={{
              display: "grid",
              gridTemplateColumns: "repeat(3, 1fr)",
              gap: "1rem",
              marginBottom: "2.5rem",
              paddingTop: "1.5rem",
              borderTop: "1px solid var(--border-subtle)",
              ...reveal(0.3),
            }}>
              {[
                { val: "200+", lbl: "Clients" },
                { val: "7j/7", lbl: "Disponibles" },
                { val: "4",    lbl: "Prestations" },
              ].map(s => (
                <div key={s.lbl} style={{ textAlign: "center" }}>
                  <p style={{
                    fontFamily: "var(--font-display)",
                    fontSize: "1.75rem", fontWeight: 500,
                    color: "var(--green-light)", lineHeight: 1,
                    marginBottom: "0.25rem",
                  }}>{s.val}</p>
                  <p style={{
                    fontSize: "0.6rem", fontWeight: 400,
                    letterSpacing: "0.18em", textTransform: "uppercase",
                    color: "var(--text-faint)",
                  }}>{s.lbl}</p>
                </div>
              ))}
            </div>

            {/* CTA */}
            <div style={{ display: "flex", alignItems: "center", gap: "1rem", flexWrap: "wrap", ...reveal(0.35) }}>
              <a
                href="/about"
                style={{
                  display: "inline-flex", alignItems: "center", gap: "0.6rem",
                  padding: "0.875rem 2rem",
                  background: "var(--gold-gradient)",
                  color: "#fff",
                  fontSize: "0.7rem", fontWeight: 600,
                  letterSpacing: "0.18em", textTransform: "uppercase",
                  fontFamily: "var(--font-body)",
                  borderRadius: "0",
                  textDecoration: "none",
                  boxShadow: "0 12px 36px rgba(60,179,113,0.3)",
                  transition: "transform var(--transition-base), box-shadow var(--transition-base)",
                }}
                onMouseEnter={e => {
                  e.currentTarget.style.transform = "translateY(-2px)";
                  e.currentTarget.style.boxShadow = "0 12px 36px rgba(60,179,113,0.3)";
                }}
                onMouseLeave={e => {
                  e.currentTarget.style.transform = "translateY(0)";
                  e.currentTarget.style.boxShadow = "var(--glow-green)";
                }}
              >
                En savoir plus
                <span style={{ fontSize: "0.9rem" }}>→</span>
              </a>

              <a
                href="/team"
                style={{
                  fontSize: "0.7rem", fontWeight: 400,
                  letterSpacing: "0.14em", textTransform: "uppercase",
                  color: "var(--text-muted)",
                  textDecoration: "none",
                  borderBottom: "1px solid var(--border-subtle)",
                  paddingBottom: "2px",
                  transition: "all var(--transition-fast)",
                }}
                onMouseEnter={e => {
                  e.currentTarget.style.color = "var(--green-primary)";
                  e.currentTarget.style.borderColor = "var(--border-gold)";
                }}
                onMouseLeave={e => {
                  e.currentTarget.style.color = "var(--text-muted)";
                  e.currentTarget.style.borderColor = "var(--border-subtle)";
                }}
              >
                Rencontrer l'équipe
              </a>
            </div>
          </div>
        </div>
      </div>

      {/* Bottom border */}
      <div style={{
        position: "absolute", bottom: 0, left: 0, right: 0, height: "1px",
        background: "linear-gradient(90deg, transparent, var(--border-subtle), transparent)",
      }} />

      <style>{`
        @media (max-width: 900px) {
          .about-grid {
            grid-template-columns: 1fr !important;
            gap: 3rem !important;
          }
        }
      `}</style>
    </div>
  );
}
