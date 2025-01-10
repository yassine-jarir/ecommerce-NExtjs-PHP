'use client';

import React, { useEffect, useState } from 'react';
import Image from 'next/image';
import { ShoppingCart, X } from 'lucide-react';

// import { Alert, AlertDescription } from '@/components/ui/alert';
import { Card, CardContent } from '@/components/ui/card';

const Products = () => {
  const [orders, setOrders] = useState([]);
  const [cart, setCart] = useState([]);
  const [isCartOpen, setIsCartOpen] = useState(false);
  const [error, setError] = useState(null);

  useEffect(() => {
    fetchOrders();
  }, []);

  const fetchOrders = async () => {
    try {
      const response = await fetch('http://localhost:4040/index.php?route=products');
      const data = await response.json();
      console.log(data);
      if (data.success) {
        setOrders(data.data);
      } else {
        setError(data.message);
      }
    } catch (error) {
      setError('Failed to fetch orders');
    }
  };

  const addToCart = (order) => {
    setCart((prevCart) => {
      const existingItem = prevCart.find((item) => item.id === order.id);
      if (existingItem) {
        return prevCart.map((item) => (item.id === order.id ? { ...item, quantity: item.quantity + 1 } : item));
      }
      return [...prevCart, { ...order, quantity: 1 }];
    });
  };

  const removeFromCart = (orderId) => {
    setCart((prevCart) => prevCart.filter((item) => item.id !== orderId));
  };

  const submitOrder = async () => {
    try {
      const orderItems = cart.map((item) => ({
        produits_id: item.id,
        quantity: item.quantity,
        prix: parseFloat(item.prix),
      }));

      const response = await fetch('http://localhost:4040/index.php?route=orders/create', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
        },
        credentials: 'include',
        body: JSON.stringify({ items: orderItems }),
      });

      const data = await response.json();

      if (data.success) {
        setCart([]);
        setIsCartOpen(false);
        fetchOrders();
      } else {
        setError(data.message);
      }
    } catch (error) {
      setError('Failed to submit order');
    }
  };

  return (
    <div className="container mx-auto p-4">
      <div className="flex justify-between items-center mb-6">
        <h1 className="text-2xl font-bold">Orders Management</h1>
        <button
          onClick={() => setIsCartOpen(!isCartOpen)}
          className="flex items-center gap-2 px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700"
        >
          <ShoppingCart className="w-5 h-5" />
          Cart ({cart.length})
        </button>
      </div>

      <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
        {orders.map((order) => (
          <Card key={order.id} className="overflow-hidden">
            <CardContent className="p-4">
              <div className=" flex flex-col justify-center items-center">
                <Image
                  width={200}
                  height={200}
                  src="https://imageio.forbes.com/specials-images/imageserve/65df2e0562b5d061b718a4af/Skin-and-hair-care-beauty-product-mock-up--lotion-bottle--oil--cream--isolated-on/960x0.jpg?format=jpg&width=1440"
                  alt=""
                />
                <div>
                  <h3 className="font-semibold">{order.name}</h3>
                  <p className="text-sm text-gray-600">Prix: ${order.prix}</p>
                </div>
                <button
                  onClick={() => addToCart(order)}
                  className="px-3 py-1 bg-blue-600 text-white text-sm rounded hover:bg-blue-700"
                >
                  Add to Cart
                </button>
              </div>
            </CardContent>
          </Card>
        ))}
      </div>

      {isCartOpen && (
        <div className="fixed inset-y-0 right-0 w-96 bg-white shadow-xl p-6 overflow-y-auto">
          <div className="flex justify-between items-center mb-4">
            <h2 className="text-xl font-bold">Shopping Cart</h2>
            <button onClick={() => setIsCartOpen(false)} className="text-gray-500 hover:text-gray-700">
              <X className="w-6 h-6" />
            </button>
          </div>

          {cart.length === 0 ? (
            <p>Your cart is empty</p>
          ) : (
            <>
              {cart.map((item) => (
                <div key={item.id} className="flex justify-between items-center py-3 border-b">
                  <div>
                    <p className="font-medium">Order #{item.id}</p>
                    <p className="text-sm text-gray-600">
                      Quantity: {item.quantity} × ${item.prix}
                    </p>
                  </div>
                  <button onClick={() => removeFromCart(item.id)} className="text-red-600 hover:text-red-700">
                    Remove
                  </button>
                </div>
              ))}

              <div className="mt-6">
                <div className="flex justify-between mb-4">
                  <span>Total:</span>
                  <span className="font-bold">
                    ${cart.reduce((total, item) => total + parseFloat(item.prix) * item.quantity, 0).toFixed(2)}
                  </span>
                </div>
                <button onClick={submitOrder} className="w-full bg-blue-600 text-white py-2 rounded hover:bg-blue-700">
                  Submit Order
                </button>
              </div>
            </>
          )}
        </div>
      )}
    </div>
  );
};

export default Products;
