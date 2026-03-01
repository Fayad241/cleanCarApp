import { useEffect, useState } from "react";

export default function HeroSection() {
  const [loaded, setLoaded] = useState(false);

  useEffect(() => {
    const t = setTimeout(() => setLoaded(true), 100);
    return () => clearTimeout(t);
  }, []);

  return (
    <section
      className="relative min-h-screen flex items-center overflow-hidden"
      style={{ backgroundColor: "var(--bg-primary)" }}
    >
      {/* ── Car Image Background avec overlay asymétrique ── */}
      <div className="absolute inset-0 z-0">
        <img
          src="https://images.unsplash.com/photo-1607860108855-64acf2078ed9?w=1920&q=80&auto=format&fit=crop"
          alt="Voiture de luxe"
          className="w-full h-full object-cover transition-all duration-1000 scale-110"
          style={{ 
            objectPosition: "center 30%", 
            opacity: 0.3,
            filter: "brightness(0.8) contrast(1.2) saturate(1.1)"
          }}
        />
        <div
          className="absolute inset-0"
          style={{
            background: `
              linear-gradient(125deg, var(--bg-primary) 0%, rgba(26,31,46,0.5) 40%, rgba(26,31,46,0.1) 80%),
              radial-gradient(circle at 70% 30%, transparent 0%, var(--bg-primary) 90%)
            `,
          }}
        />
      </div>

      {/* ── Éléments décoratifs asymétriques ── */}
      <div className="absolute inset-0 z-1 pointer-events-none">
        {/* Lignes diagonales */}
        <div
          className="absolute top-0 bottom-0 w-px rotate-12 origin-top-left"
          style={{
            left: "20%",
            background: "linear-gradient(to bottom, transparent, var(--border-gold), transparent)",
            opacity: 0.3,
          }}
        />
        <div
          className="absolute top-0 bottom-0 w-px -rotate-12 origin-top-right"
          style={{
            right: "25%",
            background: "linear-gradient(to bottom, transparent, var(--border-gold), transparent)",
            opacity: 0.3,
          }}
        />
        
        {/* Cercles décoratifs */}
        <div
          className="absolute w-64 h-64 rounded-full"
          style={{
            top: "15%",
            right: "5%",
            background: "radial-gradient(circle, var(--green-glow) 0%, transparent 70%)",
            filter: "blur(50px)",
            opacity: 0.4,
          }}
        />
        <div
          className="absolute w-96 h-96 rounded-full"
          style={{
            bottom: "-10%",
            left: "-5%",
            background: "radial-gradient(circle, var(--gold-glow) 0%, transparent 70%)",
            filter: "blur(80px)",
            opacity: 0.3,
          }}
        />
      </div>

      {/* ── Content centré avec badge flottant ── */}
      <div
        className="relative z-2 w-full mx-auto mt-24 lg:mt-30"
        style={{
          maxWidth: "var(--container-max)",
          padding: "0 var(--container-px)",
        }}
      >
        <div className="flex flex-col items-center text-center">

          {/* Badge flottant avec design asymétrique */}
          <div
            className="inline-flex items-center gap-3 px-5 py-2.5 mb-8 rounded-full relative"
            style={{
              background: "rgba(44,51,72,0.4)",
              backdropFilter: "blur(10px)",
              border: "1px solid var(--border-gold)",
              boxShadow: "var(--shadow-gold)",
              opacity: loaded ? 1 : 0,
              transform: loaded ? "translateY(0) scale(1)" : "translateY(30px) scale(0.9)",
              transition: "all 0.7s cubic-bezier(0.34, 1.56, 0.64, 1) 0.1s",
            }}
          >
            <span className="flex items-center gap-1">
              {[1,2,3,4,5].map(i => (
                <span key={i} style={{ color: "var(--green-primary)", fontSize: "0.8rem" }}>★</span>
              ))}
            </span>
            <span className="w-px h-3" style={{ background: "var(--border-gold)" }} />
            <span 
              className="text-xs tracking-wider uppercase font-medium"
              style={{ color: "var(--text-secondary)" }}
            >
              4.9 · 200+ clients
            </span>
          </div>

          {/* Titre */}
          <h1
            className="mb-6 relative"
            style={{
              fontFamily: "var(--font-display)",
              fontSize: "clamp(3.2rem, 5vw, 3.7rem)",
              fontWeight: 200,
              lineHeight: 1.2,
              letterSpacing: "-0.02em",
              color: "var(--text-primary)",
              opacity: loaded ? 1 : 0,
              transform: loaded ? "translateY(0)" : "translateY(40px)",
              transition: "all 0.8s cubic-bezier(0.34, 1.56, 0.64, 1) 0.2s",
              textShadow: "0 10px 30px rgba(0,0,0,0.5)",
            }}
          >
            <span className="inline-block relative">
              Réservez,<span className="text-gold-shimmer inline-block ml-2" style={{ fontWeight: 400 }}>Lavez,</span>
              <span className="absolute -top-6 -right-12 text-[1rem] opacity-30 rotate-12">✦</span>
            </span>
            <span className="block">Roulez satisfait.</span>
          </h1>

          {/* Sous-titre */}
          <p
            className="max-w-2xl mb-12 text-lg leading-relaxed"
            style={{
              color: "var(--text-secondary)",
              fontFamily: 'var(--font-mono)',
              fontWeight: 300,
              opacity: loaded ? 1 : 0,
              transform: loaded ? "translateY(0)" : "translateY(30px)",
              transition: "all 0.8s ease 0.4s",
            }}
          >
            Réservez votre lavage en ligne depuis chez vous. 
            <span style={{ color: "var(--green-primary)", fontWeight: 400 }}> Résultat impeccable, garanti à chaque passage.</span>
          </p>

          {/* CTA Buttons centrés avec effet de profondeur */}
          <div
            className="flex items-center justify-center gap-6 flex-wrap"
            style={{
              opacity: loaded ? 1 : 0,
              transform: loaded ? "translateY(0)" : "translateY(30px)",
              transition: "all 0.8s ease 0.5s",
            }}
          >
            <a
              href="#reservation"
              className="group relative px-12 py-4 text-sm font-bold uppercase tracking-wider overflow-hidden"
              style={{
                background: "var(--gold-gradient)",
                color: "var(--bg-primary)",
                textDecoration: "none",
                fontSize: "0.8rem",
                fontWeight: 700,
                letterSpacing: "0.2em",
                fontFamily: "var(--font-body)",
                boxShadow: "var(--shadow-gold), 0 10px 20px rgba(0,0,0,0.3)",
                borderRadius: "0",
                clipPath: "polygon(0 0, 100% 0, 95% 100%, 0 100%)",
              }}
              onMouseEnter={(e) => {
                e.currentTarget.style.transform = "translateY(-3px) scale(1.02)";
                e.currentTarget.style.boxShadow = "var(--glow-green), 0 15px 30px rgba(0,0,0,0.4)";
              }}
              onMouseLeave={(e) => {
                e.currentTarget.style.transform = "translateY(0) scale(1)";
                e.currentTarget.style.boxShadow = "var(--shadow-gold), 0 10px 20px rgba(0,0,0,0.3)";
              }}
            >
              <span className="relative z-10 flex items-center gap-2">
                Réserver maintenant
                <span className="group-hover:translate-x-2 transition-transform">→</span>
              </span>
              <span className="absolute inset-0 bg-white opacity-0 group-hover:opacity-20 transition-opacity" />
            </a>

            <a
              href="#services"
              className="group relative px-10 py-4 text-sm uppercase tracking-wider transition-all border-2 overflow-hidden"
              style={{
                borderColor: "var(--border-gold)",
                color: "var(--text-secondary)",
                textDecoration: "none",
                fontSize: "0.75rem",
                fontWeight: 400,
                letterSpacing: "0.18em",
                fontFamily: "var(--font-body)",
                background: "transparent",
                borderRadius: "0",
                clipPath: "polygon(5% 0, 100% 0, 100% 100%, 0 100%)",
              }}
              onMouseEnter={(e) => {
                e.currentTarget.style.borderColor = "var(--green-primary)";
                e.currentTarget.style.color = "var(--green-primary)";
                e.currentTarget.style.background = "rgba(60,179,113,0.1)";
                e.currentTarget.style.transform = "translateY(-2px)";
              }}
              onMouseLeave={(e) => {
                e.currentTarget.style.borderColor = "var(--border-gold)";
                e.currentTarget.style.color = "var(--text-secondary)";
                e.currentTarget.style.background = "transparent";
                e.currentTarget.style.transform = "translateY(0)";
              }}
            >
              <span className="flex items-center gap-2">
                Nos prestations
                <span className="group-hover:translate-x-2 transition-transform">→</span>
              </span>
            </a>
          </div>

          {/* Stats row avec disposition asymétrique */}
          <div
            className="flex justify-center gap-12 mt-20 mb-6 pt-8 border-t"
            style={{
              borderTopColor: "var(--border-gold)",
              opacity: loaded ? 1 : 0,
              transition: "opacity 0.8s ease 0.7s",
            }}
          >
            {[
              { value: "200+", label: "Clients" },
              { value: "4", label: "Prestations" },  
              { value: "7j/7", label: "Ouvert" },
            ].map((stat, i) => (
              <div key={stat.label} className="relative text-center">
                <p
                  style={{
                    fontFamily: "var(--font-display)",
                    fontSize: "2.2rem",
                    fontWeight: 500,
                    color: "var(--green-light)",
                    lineHeight: 1,
                    marginBottom: "0.3rem",
                    textShadow: "0 5px 15px rgba(60,179,113,0.3)",
                  }}
                >
                  {stat.value}
                </p>
                <p 
                  className="text-xs uppercase tracking-wider"
                  style={{ 
                    color: "var(--text-muted)", 
                    fontSize: "0.65rem", 
                    letterSpacing: "0.2em" 
                  }}
                >
                  {stat.label}
                </p>
              </div>
            ))}
          </div>
        </div>
      </div>

      {/* Scroll indicator vertical positionné à droite */}
      <div
        className="absolute right-8 top-1/2 -translate-y-1/2 z-2 flex flex-col items-center gap-3"
        style={{
          opacity: loaded ? 0.5 : 0,
          transition: "opacity 1s ease 1s",
        }}
      >
        <div className="relative h-24 w-px">
          <div
            className="absolute inset-0"
            style={{
              background: "linear-gradient(to bottom, transparent, var(--green-primary), transparent)",
            }}
          />
          <div
            className="absolute top-0 left-1/2 -translate-x-1/2 w-1 h-1 rounded-full animate-bounce"
            style={{ background: "var(--green-primary)" }}
          />
        </div>
        <span 
          className="text-[0.5rem] uppercase tracking-[0.2em] -rotate-90 whitespace-nowrap origin-center translate-y-12"
          style={{ color: "var(--text-muted)" }}
        >
          Scroll
        </span>
      </div>
    </section>
  );
}