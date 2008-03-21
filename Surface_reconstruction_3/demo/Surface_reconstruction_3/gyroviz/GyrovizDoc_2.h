// GyrovizDoc_2.h : interface of the CGyrovizDoc_2 class
#pragma once

// STL
#include <list>
#include <iostream>

// CGAL
#include "GyrovizKernel.h"
#include <CGAL/Constrained_Delaunay_triangulation_2.h>

// CImg
#include <CImg.h>
using namespace cimg_library;

// This demo 
#include "Gyroviz_dt2.h"


// Gyroviz's Delaunay triangulation 2-3
typedef CGAL::Triangulation_vertex_base_with_info_2<Gyroviz_info_for_dt2,K> Vb;
typedef CGAL::Triangulation_face_base_2<K> Fb;
typedef CGAL::Triangulation_data_structure_2<Vb,Fb> Tds;
typedef Gyroviz_dt2<K,Tds>         Dt2;
typedef Dt2::Face_handle Face_handle;
typedef Dt2::Finite_faces_iterator Finite_faces_iterator;
typedef Dt2::Finite_edges_iterator Finite_edges_iterator;
typedef Dt2::Finite_vertices_iterator Finite_vertices_iterator;



class CGyrovizDoc_2 : public CDocument
{
protected: // create from serialization only
  CGyrovizDoc_2();
  DECLARE_DYNCREATE(CGyrovizDoc_2)

  // Attributes
public:

  CImg<unsigned char> m_cimg_interm_image;
  CImg<unsigned char> m_cimg_gray_image;
  CImg<unsigned char> m_cimg_filt_image;
  CImg<unsigned char> m_cimg_seg_image;

  unsigned char*  m_original_image;  
  unsigned char* m_grayscaled_image;
  unsigned char* m_filtered_image;
  unsigned char* m_segmented_image;


  // Triangulation
  Dt2 m_gyroviz_dt; // The Gyroviz equation is solved on the vertices of m_gyroviz_dt

// Public methods
public:

  // Get triangulation.
  Dt2& get_dt2()
  {
    return m_gyroviz_dt;
  }
  const Dt2& get_dt2() const
  {
    return m_gyroviz_dt;
  }

  // Private methods
private:

  // misc status stuff
  void update_status();
  void status_message(char* fmt,...);
  double duration(const double time_init);

  // Overrides
public:
  virtual BOOL OnNewDocument();
  virtual void Serialize(CArchive& ar);

  // Implementation
public:
  virtual ~CGyrovizDoc_2();
#ifdef _DEBUG
  virtual void AssertValid() const;
  virtual void Dump(CDumpContext& dc) const;
#endif

protected:

  // Generated message map functions
protected:
  DECLARE_MESSAGE_MAP()
public:
  virtual BOOL OnOpenDocument(LPCTSTR lpszPathName);
  virtual BOOL OnSaveDocument(LPCTSTR lpszPathName);


  //unsigned char* cimg_image_multiplexer_char(CImg<unsigned char> image)
  //{
  //  int ix,iy,i=0;

  //  unsigned char* result = new unsigned char[image.dimx()*image.dimy()*3];

  //  for(iy=image.dimy() - 1; iy >= 0; --iy) 
  //  {
  //    for(ix=0; ix < image.dimx(); ++ix)
  //    {
  //      result[i++] = *(image.ptr() + image.dimx()*iy + ix);
  //      result[i++] = *(image.ptr() + image.dimx()*iy + ix + image.dimx()*image.dimy());
  //      result[i++] = *(image.ptr() + image.dimx()*iy + ix + image.dimx()*image.dimy()*2); 
  //    }
  //  }
  //  return result;
  //}
  unsigned char* cimg_image_multiplexer_char(CImg<unsigned char> image)
  {
    int ix,iy,i=0;

    unsigned char* result = new unsigned char[image.dimx()*image.dimy()*3];

    for(iy=0; iy < image.dimy(); ++iy) 
    {
      for(ix=0; ix < image.dimx(); ++ix)
      {
        result[i++] = *(image.ptr() + image.dimx()*iy + ix);
        result[i++] = *(image.ptr() + image.dimx()*iy + ix + image.dimx()*image.dimy());
        result[i++] = *(image.ptr() + image.dimx()*iy + ix + image.dimx()*image.dimy()*2); 
      }
    }
    return result;
  }

};


