import Navbar from "../../components/layout/Navbar";
import HeroSection from "./sections/HeroSection";
import BookingSection from "./sections/BookingSection";
import ServicesSection from "./sections/ServicesSection";
import WhyUsSection from "./sections/WhyUsSection";
import LoyaltySection from "./sections/LoyaltySection";
import TestimonialsSection from "./sections/TestimonialsSection";
import { HoursSection } from "./sections/HoursSection";
import { Footer } from "../../components/layout/Footer";

export default function HomePage() {
  return (
    <div style={{ minHeight: "100vh", backgroundColor: "var(--bg-primary)" }}>
      <Navbar isAuthenticated={false} />
      <HeroSection />
      <BookingSection />
      <ServicesSection />
      <WhyUsSection />
      <LoyaltySection />
      <TestimonialsSection />
      <HoursSection />
      <Footer />
    </div>
  );
}
