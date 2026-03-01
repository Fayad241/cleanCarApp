import { useState, useEffect, useRef } from "react";

interface Service {
  id: string;
  name: string;
  description: string;
  is_popular: boolean;
  pricing: { vehicule_size: string; price: number; duration_minutes: number }[];
}

export default function ServicesSection() {
  const sectionRef = useRef<HTMLElement>(null);
  const [visible, setVisible] = useState(false);
  const [services, setServices] = useState<Service[]>([]);
  const [hovered, setHovered] = useState<string | null>(null);

  useEffect(() => {
    const observer = new IntersectionObserver(
      ([e]) => { if (e.isIntersecting) { setVisible(true); observer.disconnect(); } },
      { threshold: 0.1 }
    );
    if (sectionRef.current) observer.observe(sectionRef.current);
    return () => observer.disconnect();
  }, []);

  useEffect(() => {
    fetch(`${import.meta.env.VITE_API_URL}/services`)
      .then((r) => r.json())
      .then((d) => setServices(d.data?.services || []));
  }, []);

  const getMinPrice = (s: Service) =>
    s.pricing?.length ? Math.min(...s.pricing.map((p) => p.price)) : null;

  return (
    <section
      ref={sectionRef}
      id="services"
      className="w-full"
      style={{ 
        backgroundColor: "var(--bg-primary)", 
        padding: "var(--section-py) var(--container-px)" 
      }}
    >
      <div className="mx-auto" style={{ maxWidth: "var(--container-max)" }}>

        {/* Header */}
        <div
          className="flex justify-between items-end flex-wrap gap-6 mb-14 transition-all"
          style={{
            opacity: visible ? 1 : 0,
            transform: visible ? "translateY(0)" : "translateY(24px)",
            transition: "all 0.7s ease",
          }}
        >
          <div>
            <p 
              className="text-[0.69rem] uppercase tracking-wider mb-3"
              style={{ 
                color: "var(--green-primary)", 
                letterSpacing: "0.3em" 
              }}
            >
              Nos Prestations
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
              Propre, brillant
              <br />
              <span className="text-gold">impeccable</span>
            </h2>
          </div>
          <a
            href="/services"
            className="text-[0.7rem] uppercase tracking-wider no-underline pb-[2px] transition-all"
            style={{
              color: "var(--text-muted)",
              letterSpacing: "0.15em",
              borderBottom: "1px solid var(--border-subtle)",
              transition: "all var(--transition-base)",
            }}
            onMouseEnter={(e) => {
              e.currentTarget.style.color = "var(--green-primary)";
              e.currentTarget.style.borderColor = "var(--border-gold)";
            }}
            onMouseLeave={(e) => {
              e.currentTarget.style.color = "var(--text-muted)";
              e.currentTarget.style.borderColor = "var(--border-subtle)";
            }}
          >
            Voir tous →
          </a>
        </div>

        <div className="relative">  

          {/* Grid */}
          <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 relative">
            {services.map((service, i) => (
              <div
                key={service.id}
                onMouseEnter={() => setHovered(service.id)}
                onMouseLeave={() => setHovered(null)}
                className="relative p-10 cursor-pointer overflow-hidden transition-all"
                style={{
                  background: "var(--bg-secondary)", 
                  transition: "all var(--transition-base)",
                  opacity: visible ? 1 : 0,
                  transform: visible ? "translateY(0)" : "translateY(32px)",
                  transitionDelay: `${i * 0.08}s`,
                  transitionDuration: "0.6s",
                  boxShadow: hovered === service.id ? "var(--shadow-md), var(--glow-soft)" : "none",
                  zIndex: hovered === service.id ? 10 : 1,
                }}
              >
                {/* Popular badge - toujours visible */}
                {service.is_popular && (
                  <div
                    className="absolute top-5 right-5 px-2 py-1 text-[0.55rem] uppercase tracking-wider"
                    style={{
                      background: "var(--gold-glow)",
                      border: "1px solid var(--border-gold)",
                      color: "var(--green-primary)",
                      letterSpacing: "0.2em",
                    }}
                  >
                    Populaire
                  </div>
                )}

                {/* Index number */}
                <span
                  className="block mb-5 text-5xl font-light leading-none transition-colors"
                  style={{
                    fontFamily: "var(--font-display)",
                    color: "var(--border-gold)", 
                  }}
                >
                  {String(i + 1).padStart(2, "0")}
                </span>

                {/* Name */}
                <h3
                  className="mb-3 text-2xl leading-tight"
                  style={{
                    fontFamily: "var(--font-display)",
                    fontWeight: 400,
                    color: "var(--gold-deep)", 
                  }}
                >
                  {service.name}
                </h3>

                {/* Description */}
                <p
                  className="mb-8 text-[14.4px] leading-relaxed min-h-[3.5rem]"
                  style={{
                    color: "var(--text-secondary)", 
                    fontWeight: 300,
                  }}
                >
                  {service.description || "Service de lavage professionnel premium."}
                </p>

                {/* Footer */}
                <div className="flex items-end justify-between">
                  <div>
                    <p 
                      className="text-[0.55rem] uppercase tracking-wider mb-1"
                      style={{ 
                        color: "var(--text-muted)", 
                        letterSpacing: "0.2em" 
                      }}
                    >
                      À partir de
                    </p>
                    <p 
                      className="text-[1.6rem] font-medium leading-none"
                      style={{ 
                        fontFamily: "var(--font-display)", 
                        color: "var(--text-primary)" 
                      }}
                    >
                      {getMinPrice(service)?.toLocaleString()}
                      <span 
                        className="text-xs ml-1 font-light"
                        style={{ 
                          color: "var(--text-muted)", 
                          fontFamily: "var(--font-body)" 
                        }}
                      >FCFA</span>
                    </p>
                  </div>
                  <div
                    className="w-9 h-9 flex items-center justify-center transition-all"
                    style={{
                      border: `1px solid ${hovered === service.id ? "var(--border-gold)" : "var(--border-subtle)"}`,
                      transition: "border-color var(--transition-base)",
                      backgroundColor: hovered === service.id ? "var(--gold-glow)" : "transparent",
                    }}
                  >
                    <span 
                      className="text-xs transition-colors"
                      style={{ 
                        color: hovered === service.id ? "var(--green-primary)" : "var(--text-muted)",
                        transition: "color var(--transition-base)" 
                      }}
                    >
                      →
                    </span>
                  </div>
                </div>

                {/* Bottom animated line */}
                <div
                  className="absolute bottom-0 left-0 h-px transition-all"
                  style={{
                    width: hovered === service.id ? "100%" : "0%",
                    background: "var(--gold-gradient)",
                    transition: "width 0.4s ease",
                  }}
                />
              </div>
            ))}
          </div>
        </div>
      </div>
    </section>
  );
}