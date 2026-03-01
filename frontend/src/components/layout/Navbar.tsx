import { useState, useEffect } from "react";

interface NavbarProps {
  isAuthenticated?: boolean;
}

export default function Navbar({ isAuthenticated = false }: NavbarProps) {
  const [scrolled, setScrolled] = useState(false);
  const [menuOpen, setMenuOpen] = useState(false);

  useEffect(() => {
    const handleScroll = () => setScrolled(window.scrollY > 60);
    window.addEventListener("scroll", handleScroll);
    return () => window.removeEventListener("scroll", handleScroll);
  }, []);

  return (
    <nav
      className="fixed top-0 left-0 right-0 z-100 transition-all"
      style={{
        padding: scrolled ? "0.75rem 0" : "1.5rem 0",
        backgroundColor: scrolled ? "var(--bg-overlay)" : "transparent",
        backdropFilter: scrolled ? "blur(20px)" : "none",
        borderBottom: scrolled ? "1px solid var(--border-gold)" : "1px solid transparent",
        transition: "all var(--transition-slow)",
      }}
    >
      <div
        className="mx-auto flex items-center justify-between"
        style={{
          maxWidth: "var(--container-max)",
          padding: "0 var(--container-px)",
        }}
      >
        {/* Logo */}
        <a href="/" className="flex items-center gap-3 no-underline">
          <div
            className="w-9 h-9 rounded-full flex items-center justify-center"
            style={{
              background: "var(--gold-gradient)",
              boxShadow: "var(--shadow-gold)",
            }}
          >
            <span style={{ color: "var(--bg-primary)", fontWeight: 800, fontSize: "11px", fontFamily: "var(--font-body)" }}>CC</span>
          </div>
          <span
            style={{
              fontFamily: "var(--font-display)",
              fontSize: "1.25rem",
              fontWeight: 400,
              color: "var(--text-primary)",
              letterSpacing: "0.1em",
            }}
          >
            Clean<span style={{ color: "var(--green-primary)" }}>Car</span>
          </span>
        </a>

        {/* Links - Desktop */}
        <div className="hidden-mobile flex items-center gap-10">
          {[
            { label: "Accueil", href: "/" },
            { label: "Services", href: "#services" },
            { label: "À propos", href: "#about" },
          ].map((link) => (
            <a
              key={link.label}
              href={link.href}
              className="no-underline text-xs uppercase tracking-wider transition-colors"
              style={{
                color: "var(--text-secondary)",
                fontSize: "0.75rem",
                letterSpacing: "0.12em",
                fontFamily: "var(--font-body)",
                fontWeight: 400,
                transition: "color var(--transition-base)",
              }}
              onMouseEnter={(e) => (e.currentTarget.style.color = "var(--green-primary)")}
              onMouseLeave={(e) => (e.currentTarget.style.color = "var(--text-secondary)")}
            >
              {link.label}
            </a>
          ))}
        </div>

        {/* CTA */}
        <div className="hidden-mobile flex items-center gap-4">
          {isAuthenticated ? (
            <>
              <a
                href="/dashboard"
                className="no-underline text-xs uppercase tracking-wider transition-colors"
                style={{
                  color: "var(--text-secondary)",
                  fontSize: "0.75rem",
                  letterSpacing: "0.12em",
                  fontFamily: "var(--font-body)",
                  transition: "color var(--transition-base)",
                }}
                onMouseEnter={(e) => (e.currentTarget.style.color = "var(--text-primary)")}
                onMouseLeave={(e) => (e.currentTarget.style.color = "var(--text-secondary)")}
              >
                Mon compte
              </a>
              <a href="/reservation" style={btnStyle}>Réserver</a>
            </>
          ) : (
            <>
              <a
                href="/login"
                className="no-underline text-xs uppercase tracking-wider transition-colors"
                style={{
                  color: "var(--text-secondary)",
                  fontSize: "0.75rem",
                  letterSpacing: "0.12em",
                  fontFamily: "var(--font-body)",
                  transition: "color var(--transition-base)",
                }}
                onMouseEnter={(e) => (e.currentTarget.style.color = "var(--text-primary)")}
                onMouseLeave={(e) => (e.currentTarget.style.color = "var(--text-secondary)")}
              >
                Connexion
              </a>
              <a href="/register" style={btnStyle}>S'inscrire</a>
            </>
          )}
        </div>

        {/* Hamburger */}
        <button
          onClick={() => setMenuOpen(!menuOpen)}
          className="show-mobile hidden flex-col gap-1 p-1 bg-transparent border-none cursor-pointer outline-none"
          style={{ display: "none" }}
        >
          {[0, 1, 2].map((i) => (
            <span
              key={i}
              className="block w-[22px] h-px transition-all"
              style={{
                backgroundColor: "var(--text-primary)",
                transition: "all var(--transition-base)",
                transformOrigin: "center",
                transform:
                  menuOpen
                    ? i === 0 ? "rotate(45deg) translate(4px, 4px)"
                    : i === 2 ? "rotate(-45deg) translate(4px, -4px)"
                    : "scaleX(0)"
                    : "none",
                opacity: menuOpen && i === 1 ? 0 : 1,
              }}
            />
          ))}
        </button>
      </div>

      {/* Mobile Menu */}
      <div
        style={{
          maxHeight: menuOpen ? "500px" : "0px",
          opacity: menuOpen ? 1 : 0,
          transform: menuOpen ? "translateY(0)" : "translateY(-10px)",
          overflow: "hidden",
          transition: "all 0.35s cubic-bezier(0.4, 0, 0.2, 1)",
          backgroundColor: "var(--bg-card)",
          borderTop: "1px solid var(--border-gold)",
          backdropFilter: "blur(12px)",
          marginTop: menuOpen ? "0.9rem" : "0",
        }}
      >
        <div
          style={{
            display: "flex",
            flexDirection: "column",
            gap: "1.5rem",
            padding: "2rem 1.5rem",
          }}
        >
          {[
            { label: "Accueil", href: "/" },
            { label: "Services", href: "#services" },
            { label: "À propos", href: "#about" },
          ].map((link) => (
            <a
              key={link.label}
              href={link.href}
              onClick={() => setMenuOpen(false)}
              style={{
                textDecoration: "none",
                fontSize: "0.9rem",
                letterSpacing: "0.18em",
                textTransform: "uppercase",
                fontFamily: "var(--font-body)",
                color: "var(--text-secondary)",
                transition: "all 0.25s ease",
              }}
              onMouseEnter={(e) => {
                e.currentTarget.style.color = "var(--gold-primary)";
                e.currentTarget.style.transform = "translateX(4px)";
              }}
              onMouseLeave={(e) => {
                e.currentTarget.style.color = "var(--text-secondary)";
                e.currentTarget.style.transform = "translateX(0)";
              }}
            >
              {link.label}
            </a>
          ))}

          <div
            style={{
              marginTop: "1.5rem",
              paddingTop: "1.5rem",
              borderTop: "1px solid var(--border-subtle)",
              display: "flex",
              flexDirection: "column",
              gap: "1rem",
            }}
          >
            <a
              href="/login"
              style={{
                textDecoration: "none",
                fontSize: "0.85rem",
                letterSpacing: "0.15em",
                textTransform: "uppercase",
                color: "var(--text-secondary)",
              }}
            >
              Connexion
            </a>

            <a
              href="/register"
              style={{
                ...btnStyle,
                textAlign: "center",
                padding: "0.8rem",
                fontSize: "0.75rem",
                borderRadius: "6px",
              }}
            >
              Réserver
            </a>
          </div>
        </div>
      </div>

      <style>{`
        @media (max-width: 768px) {
          .hidden-mobile { display: none !important; }
          .show-mobile { display: flex !important; }
        }
      `}</style>
    </nav>
  );
}

const btnStyle: React.CSSProperties = {
  padding: "0.6rem 1.5rem",
  background: "var(--gold-gradient)",
  color: "var(--bg-primary)",
  textDecoration: "none",
  fontSize: "0.7rem",
  fontWeight: 600,
  letterSpacing: "0.15em",
  textTransform: "uppercase",
  fontFamily: "var(--font-body)",
  transition: "all var(--transition-base)",
  boxShadow: "var(--shadow-gold)",
  border: "none",
  cursor: "pointer",
};