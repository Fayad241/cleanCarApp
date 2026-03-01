import { useState, useEffect, useRef } from "react";

interface DayHours {
  is_open: boolean;
  open: string | null;
  close: string | null;
}

interface OpeningHours {
  monday: DayHours;
  tuesday: DayHours;
  wednesday: DayHours;
  thursday: DayHours;
  friday: DayHours;
  saturday: DayHours;
  sunday: DayHours;
}

const DAY_MAP: { en: keyof OpeningHours; fr: string }[] = [
  { en: "monday",    fr: "Lundi" },
  { en: "tuesday",   fr: "Mardi" },
  { en: "wednesday", fr: "Mercredi" },
  { en: "thursday",  fr: "Jeudi" },
  { en: "friday",    fr: "Vendredi" },
  { en: "saturday",  fr: "Samedi" },
  { en: "sunday",    fr: "Dimanche" },
];

export function HoursSection() {
  const sectionRef = useRef<HTMLElement>(null);
  const [visible, setVisible] = useState(false);
  const [hours, setHours] = useState<{ day: string; open: string; close: string; isOpen: boolean }[]>([]);

  useEffect(() => {
    const observer = new IntersectionObserver(
      ([e]) => { if (e.isIntersecting) { setVisible(true); observer.disconnect(); } },
      { threshold: 0.1 }
    );
    if (sectionRef.current) observer.observe(sectionRef.current);
    return () => observer.disconnect();
  }, []);

  // Charger les horaires depuis l'API
  useEffect(() => {
    fetch(`${import.meta.env.VITE_API_URL}/station/hours`)
      .then((r) => r.json())
      .then((data) => {
        const opening: OpeningHours = data.data;
        if (!opening) return;

        const mapped = DAY_MAP.map(({ en, fr }) => {
          const h = opening[en];
          return {
            day: fr,
            open: h?.open ?? "08:00",
            close: h?.close ?? "19:00",
            isOpen: h?.is_open ?? true,
          };
        });

        setHours(mapped);
      })
      .catch(() => {
        // Fallback si API non disponible
        setHours(DAY_MAP.map(({ fr }, i) => ({
          day: fr,
          open: i === 6 ? "09:00" : "08:00",
          close: i === 6 ? "17:00" : "19:00",
          isOpen: true,
        })));
      });
  }, []);

  const todayName = new Date().toLocaleDateString("fr-FR", { weekday: "long" });
  const todayCapitalized = todayName.charAt(0).toUpperCase() + todayName.slice(1);

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
        <div className="grid grid-cols-1 lg:grid-cols-2 items-start gap-20 hours-grid">

          {/* Horaires */}
          <div
            className="transition-all"
            style={{
              opacity: visible ? 1 : 0,
              transform: visible ? "translateX(0)" : "translateX(-24px)",
              transition: "all 0.7s ease",
            }}
          >
            <p 
              className="text-[0.65rem] uppercase tracking-wider mb-3"
              style={{
                color: "var(--green-primary)",
                letterSpacing: "0.3em",
              }}
            >
              Horaires d'ouverture
            </p>
            <h2 
              className="font-light mb-10 leading-tight"
              style={{
                fontFamily: "var(--font-display)",
                fontSize: "clamp(1.75rem, 3vw, 2.5rem)",
                color: "var(--text-primary)",
              }}
            >
              Ouverts <span className="text-gold">7j / 7</span>
            </h2>

            <div className="flex flex-col">
              {hours.map((h, i) => {
                const isToday = h.day === todayCapitalized;
                return (
                  <div
                    key={h.day}
                    className="flex justify-between items-center py-3.5"
                    style={{
                      borderBottom: "1px solid var(--border-subtle)",
                    }}
                  >
                    <div className="flex items-center gap-3">
                      {isToday && (
                        <span 
                          className="inline-block w-[5px] h-[5px] rounded-full flex-shrink-0"
                          style={{
                            background: "var(--green-primary)",
                          }}
                        />
                      )}
                      <span 
                        className="text-sm"
                        style={{
                          fontWeight: isToday ? 400 : 300,
                          color: isToday ? "var(--green-light)" : "var(--text-secondary)",
                          marginLeft: isToday ? 0 : "13px",
                        }}
                      >
                        {h.day}
                        {isToday && (
                          <span 
                            className="ml-2 text-[0.55rem] uppercase tracking-wider"
                            style={{
                              color: "var(--gold-deep)",
                              letterSpacing: "0.15em",
                            }}
                          >
                            Aujourd'hui
                          </span>
                        )}
                      </span>
                    </div>
                    <span 
                      className="text-sm"
                      style={{
                        fontWeight: isToday ? 400 : 300,
                        color: isToday ? "var(--text-primary)" : "var(--text-muted)",
                      }}
                    >
                      {h.isOpen ? `${h.open} – ${h.close}` : "Fermé"}
                    </span>
                  </div>
                );
              })}
            </div>
          </div>

          {/* Localisation */}
          <div
            className="transition-all"
            style={{
              opacity: visible ? 1 : 0,
              transform: visible ? "translateX(0)" : "translateX(24px)",
              transition: "all 0.7s ease 0.2s",
            }}
          >
            <p 
              className="text-[0.65rem] uppercase tracking-wider mb-3"
              style={{
                color: "var(--green-primary)",
                letterSpacing: "0.3em",
              }}
            >
              Nous trouver
            </p>
            <h2 
              className="font-light mb-8 leading-tight"
              style={{
                fontFamily: "var(--font-display)",
                fontSize: "clamp(1.75rem, 3vw, 2.5rem)",
                color: "var(--text-primary)",
              }}
            >
              Station de <span className="text-gold">Cotonou</span>
            </h2>

            {/* Map placeholder */}
            <div 
              className="relative h-[200px] mb-8 overflow-hidden flex items-center justify-center"
              style={{
                background: "var(--bg-card)",
                border: "1px solid var(--border-gold)",
              }}
            >
              {/* Grid lines */}
              <div 
                className="absolute inset-0"
                style={{
                  backgroundImage: `
                    repeating-linear-gradient(0deg, var(--border-subtle) 0px, var(--border-subtle) 1px, transparent 1px, transparent 40px),
                    repeating-linear-gradient(90deg, var(--border-subtle) 0px, var(--border-subtle) 1px, transparent 1px, transparent 40px)
                  `,
                }}
              />
              {/* Gold glow */}
              <div 
                className="absolute w-[120px] h-[120px] rounded-full"
                style={{
                  background: "radial-gradient(circle, var(--gold-glow) 0%, transparent 70%)",
                  filter: "blur(20px)",
                }}
              />
              {/* Pin */}
              <div className="relative z-10 text-center">
                <div
                  className="animate-pulse-gold w-3 h-3 rounded-full mx-auto mb-2"
                  style={{
                    background: "var(--green-primary)",
                    boxShadow: "0 0 0 4px var(--gold-glow)",
                  }}
                />
                <p className="text-xs" style={{ color: "var(--text-secondary)" }}>Clean Car Pro</p>
                <p className="text-[0.65rem]" style={{ color: "var(--text-muted)" }}>Cotonou, Bénin</p>
              </div>
            </div>

            {/* Contact info */}
            <div className="flex flex-col gap-4">
              {[
                { icon: "📍", text: "Avenue 123, Cotonou, Bénin" },
                { icon: "📞", text: "+229 01 57 00 00" },
                { icon: "📧", text: "contact@cleancar.bj" },
              ].map((item, i) => (
                <div key={i} className="flex items-center gap-3.5">
                  <span 
                    className="text-sm flex-shrink-0"
                    style={{ color: "var(--gold-deep)" }}
                  >
                    {item.icon}
                  </span>
                  <span 
                    className="text-sm font-light"
                    style={{ color: "var(--text-secondary)" }}
                  >
                    {item.text}
                  </span>
                </div>
              ))}
            </div>
          </div>
        </div>
      </div>

      <style>{`
        @media (max-width: 1024px) {
          .hours-grid { gap: 3rem !important; }
        }
      `}</style>
    </section>
  );
}