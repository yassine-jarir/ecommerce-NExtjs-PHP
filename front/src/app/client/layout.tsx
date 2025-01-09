import Navbar from "./Navbar.jsx/Navbar";
  
export default async function RootLayout({
  children,
}: {
  children: React.ReactNode;
}) {
 
  return (
 <>
      
   
      <main className="pointer-events-auto">
          <Navbar
 
          />
            {children}
 
          </main>
          {/* <Footer /> */}
 </>
 
  );
}
