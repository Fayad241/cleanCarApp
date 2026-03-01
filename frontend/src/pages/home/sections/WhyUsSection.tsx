import { useState, useEffect, useRef } from "react";

const features = [
  {
    icon: "◷",
    title: "Réservation 24h/24",
    desc: "Réservez à tout moment depuis votre téléphone, sans file d'attente ni appel téléphonique.",
  },
  {
    icon: "✦",
    title: "Produits premium",
    desc: "Produits certifiés haut de gamme, respectueux de votre carrosserie et de l'environnement.",
  },
  {
    icon: "◎",
    title: "Station à Cotonou",
    desc: "Idéalement située, facile d'accès, avec un parking spacieux pour votre confort.",
  },
  {
    icon: "⬡",
    title: "Programme fidélité",
    desc: "Gagnez des points à chaque visite et échangez-les contre des réductions ou un lavage gratuit.",
  },
];

export default function WhyUsSection() {
  const sectionRef = useRef<HTMLElement>(null);
  const [visible, setVisible] = useState(false);
  const [hovered, setHovered] = useState<number | null>(null);

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
              fontFamily: "var(--font-body)",
            }}
          >
            Pourquoi nous choisir
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
            Ce qui nous {" "}
            <span className="text-gold">distingue</span>
          </h2>
        </div>

        {/* Cards */}
        <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
          {features.map((f, i) => (
            <div
              key={i}
              onMouseEnter={() => setHovered(i)}
              onMouseLeave={() => setHovered(null)}
              className="p-8 transition-all cursor-default"
              style={{
                background: hovered === i ? "var(--bg-card-hover)" : "var(--bg-card)",
                border: `1px solid ${hovered === i ? "var(--border-gold)" : "var(--border-subtle)"}`,
                transition: "all var(--transition-base)",
                opacity: visible ? 1 : 0,
                transform: visible ? "translateY(0)" : "translateY(32px)",
                transitionDelay: `${i * 0.1}s`,
                transitionDuration: "0.6s",
              }}
            >
              <div 
                className="text-2xl mb-5 transition-colors"
                style={{
                  color: hovered === i ? "var(--green-primary)" : "var(--gold-deep)",
                  transition: "color var(--transition-base)",
                }}
              >
                {f.icon}
              </div>
              <h3 
                className="mb-3"
                style={{
                  fontFamily: "var(--font-display)",
                  fontSize: "1.15rem",
                  fontWeight: 400,
                  color: "var(--text-primary)",
                }}
              >
                {f.title}
              </h3>
              <p 
                className="text-sm leading-relaxed font-light"
                style={{
                  color: "var(--text-secondary)",
                  lineHeight: 1.75,
                }}
              >
                {f.desc}
              </p>
            </div>
          ))}
        </div>
      </div>
    </section>
  );
}